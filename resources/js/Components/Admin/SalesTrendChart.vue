<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    days: {
        type: Array,
        default: () => ['Thu', 'Fri', 'Sat', 'Sun', 'Mon', 'Tue', 'Today'],
    },
    revenue: {
        type: Array,
        default: () => [0, 0, 0, 0, 0, 0, 0],
    },
    orders: {
        type: Array,
        default: () => [0, 0, 0, 0, 0, 0, 0],
    },
});

// View mode: 'revenue' | 'orders' | 'combined'
const viewMode = ref('revenue');

// Hovered state (defaults to last index)
const hoveredIndex = ref(null);
const activeIndex = computed(() => {
    if (hoveredIndex.value !== null && hoveredIndex.value >= 0 && hoveredIndex.value < props.days.length) {
        return hoveredIndex.value;
    }
    return props.days.length - 1;
});

// Calculate Max for Y-Axis Scale
const maxRevenueRaw = computed(() => Math.max(...props.revenue.map(Number), 0));
const maxOrdersRaw = computed(() => Math.max(...props.orders.map(Number), 0));

// High-Definition SVG Coordinate System (1200 x 420 for razor-sharp rendering)
const svgWidth = 1200;
const svgHeight = 420;
const paddingLeft = 100;
const paddingRight = 50;
const paddingTop = 45;
const paddingBottom = 60;

const chartInnerWidth = svgWidth - paddingLeft - paddingRight;
const chartInnerHeight = svgHeight - paddingTop - paddingBottom;
const baseLineY = paddingTop + chartInnerHeight;

// Y-Domain: Baseline min is ALWAYS strictly 0
const yDomain = computed(() => {
    if (viewMode.value === 'orders') {
        const max = Math.max(Math.ceil(maxOrdersRaw.value / 4) * 4, 8);
        return { min: 0, max, isCurrency: false };
    } else {
        const max = Math.max(Math.ceil((maxRevenueRaw.value * 1.25) / 4000) * 4000, 20000);
        return { min: 0, max, isCurrency: true };
    }
});

// Y-Axis Ticks (6 evenly spaced tiers)
const yAxisTicks = computed(() => {
    const ticks = [];
    const count = viewMode.value === 'orders' ? 5 : 6;
    const { min, max, isCurrency } = yDomain.value;
    const range = max - min || 1;

    for (let i = count - 1; i >= 0; i--) {
        const value = Math.round(min + (range / (count - 1)) * i);
        const y = paddingTop + chartInnerHeight * (1 - (value - min) / range);
        const label = isCurrency ? `৳${value.toLocaleString()}` : `${value}`;
        ticks.push({ label, y, value });
    }
    return ticks;
});

// Coordinate Points calculation with strict clamping to prevent negative/overflow coordinates
const getPoints = (values, domain) => {
    const len = values.length;
    if (len === 0) return [];
    const step = chartInnerWidth / Math.max(len - 1, 1);
    const { min, max } = domain;
    const range = max - min || 1;

    return values.map((val, idx) => {
        const numVal = Math.max(Number(val) || 0, 0);
        const x = paddingLeft + idx * step;
        const normalized = Math.min(Math.max((numVal - min) / range, 0), 1);
        const y = paddingTop + chartInnerHeight * (1 - normalized);
        return { x, y, value: numVal, index: idx };
    });
};

const revenuePoints = computed(() => getPoints(props.revenue, yDomain.value));
const orderPoints = computed(() => {
    const ordersDomain = { min: 0, max: Math.max(maxOrdersRaw.value, 1) };
    return getPoints(props.orders, viewMode.value === 'combined' ? ordersDomain : yDomain.value);
});

// Fritsch-Carlson Monotone Cubic Spline (Guaranteed Monotonicity & Zero-Overshoot)
const createMonotonePath = (points) => {
    const n = points.length;
    if (n === 0) return '';
    if (n === 1) return `M ${points[0].x.toFixed(1)} ${points[0].y.toFixed(1)}`;
    if (n === 2) {
        return `M ${points[0].x.toFixed(1)} ${points[0].y.toFixed(1)} L ${points[1].x.toFixed(1)} ${points[1].y.toFixed(1)}`;
    }

    // 1. Secant slopes
    const dx = [];
    const dy = [];
    const slopes = [];
    for (let i = 0; i < n - 1; i++) {
        const dxi = points[i + 1].x - points[i].x;
        const dyi = points[i + 1].y - points[i].y;
        dx.push(dxi);
        dy.push(dyi);
        slopes.push(dxi === 0 ? 0 : dyi / dxi);
    }

    // 2. Initial tangents
    const tangents = new Array(n);
    tangents[0] = slopes[0];
    for (let i = 1; i < n - 1; i++) {
        if (slopes[i - 1] * slopes[i] <= 0) {
            tangents[i] = 0; // Peak or flat baseline tangent
        } else {
            tangents[i] = (slopes[i - 1] + slopes[i]) / 2;
        }
    }
    tangents[n - 1] = slopes[n - 2];

    // 3. Fritsch-Carlson constraint
    for (let i = 0; i < n - 1; i++) {
        if (slopes[i] === 0) {
            tangents[i] = 0;
            tangents[i + 1] = 0;
        } else {
            const alpha = tangents[i] / slopes[i];
            const beta = tangents[i + 1] / slopes[i];
            const dist = alpha * alpha + beta * beta;
            if (dist > 9) {
                const tau = 3 / Math.sqrt(dist);
                tangents[i] = tau * alpha * slopes[i];
                tangents[i + 1] = tau * beta * slopes[i];
            }
        }
    }

    // 4. Construct SVG Bézier path with strict baseline clamping
    let path = `M ${points[0].x.toFixed(1)} ${points[0].y.toFixed(1)}`;
    for (let i = 0; i < n - 1; i++) {
        const segmentDx = dx[i] / 3;
        const cp1x = points[i].x + segmentDx;
        let cp1y = points[i].y + tangents[i] * segmentDx;

        const cp2x = points[i + 1].x - segmentDx;
        let cp2y = points[i + 1].y - tangents[i + 1] * segmentDx;

        // Strictly clamp control points to not dip below baseline or above ceiling
        cp1y = Math.min(Math.max(cp1y, paddingTop), baseLineY);
        cp2y = Math.min(Math.max(cp2y, paddingTop), baseLineY);

        path += ` C ${cp1x.toFixed(1)} ${cp1y.toFixed(1)}, ${cp2x.toFixed(1)} ${cp2y.toFixed(1)}, ${points[i + 1].x.toFixed(1)} ${points[i + 1].y.toFixed(1)}`;
    }

    return path;
};

// SVG Paths
const revenueLinePath = computed(() => createMonotonePath(revenuePoints.value));
const revenueAreaPath = computed(() => {
    if (revenuePoints.value.length === 0) return '';
    const first = revenuePoints.value[0];
    const last = revenuePoints.value[revenuePoints.value.length - 1];
    return `${revenueLinePath.value} L ${last.x.toFixed(1)} ${baseLineY.toFixed(1)} L ${first.x.toFixed(1)} ${baseLineY.toFixed(1)} Z`;
});

const ordersLinePath = computed(() => createMonotonePath(orderPoints.value));
const ordersAreaPath = computed(() => {
    if (orderPoints.value.length === 0) return '';
    const first = orderPoints.value[0];
    const last = orderPoints.value[orderPoints.value.length - 1];
    return `${ordersLinePath.value} L ${last.x.toFixed(1)} ${baseLineY.toFixed(1)} L ${first.x.toFixed(1)} ${baseLineY.toFixed(1)} Z`;
});

// Hit-box slice width
const colSliceWidth = computed(() => {
    const len = props.days.length;
    if (len <= 1) return svgWidth;
    return chartInnerWidth / (len - 1);
});

// Mouse tracking
const chartContainerRef = ref(null);
const handleMouseMove = (e) => {
    if (!chartContainerRef.value || props.days.length === 0) return;
    const rect = chartContainerRef.value.getBoundingClientRect();
    const clientX = e.clientX - rect.left;
    const svgX = (clientX / rect.width) * svgWidth;

    const step = chartInnerWidth / Math.max(props.days.length - 1, 1);
    const rawIdx = Math.round((svgX - paddingLeft) / step);
    const clampedIdx = Math.max(0, Math.min(props.days.length - 1, rawIdx));
    hoveredIndex.value = clampedIdx;
};
</script>

<template>
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm transition-all select-none overflow-hidden">
        <!-- Top Header & Tabs Switcher -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100/80 dark:border-slate-800/80">
            <div>
                <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white font-serif tracking-tight">
                    Revenue & Orders Activity
                </h3>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">
                    7-Day live sales and order performance
                </p>
            </div>

            <!-- View Switcher Tabs -->
            <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100/90 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 self-start sm:self-auto">
                <button
                    type="button"
                    @click="viewMode = 'revenue'"
                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                    :class="viewMode === 'revenue' 
                        ? 'bg-[#730163] text-white shadow-sm' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                >
                    <span>💰</span>
                    <span>Revenue (৳)</span>
                </button>

                <button
                    type="button"
                    @click="viewMode = 'orders'"
                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                    :class="viewMode === 'orders' 
                        ? 'bg-[#730163] text-white shadow-sm' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                >
                    <span>📦</span>
                    <span>Orders</span>
                </button>

                <button
                    type="button"
                    @click="viewMode = 'combined'"
                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                    :class="viewMode === 'combined' 
                        ? 'bg-[#730163] text-white shadow-sm' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                >
                    <span>📊</span>
                    <span>Combined</span>
                </button>
            </div>
        </div>

        <!-- High-Resolution SVG Chart Container with Vector Precision -->
        <div
            ref="chartContainerRef"
            class="relative w-full overflow-visible cursor-pointer pt-4"
            @mousemove="handleMouseMove"
            @mouseleave="hoveredIndex = null"
        >
            <svg
                :viewBox="`0 0 ${svgWidth} ${svgHeight}`"
                class="w-full h-72 sm:h-96 overflow-visible"
                shape-rendering="geometricPrecision"
                text-rendering="geometricPrecision"
            >
                <defs>
                    <!-- Clean Luxury Purple Area Gradient -->
                    <linearGradient id="cleanPurpleGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#730163" stop-opacity="0.28" />
                        <stop offset="65%" stop-color="#730163" stop-opacity="0.06" />
                        <stop offset="100%" stop-color="#730163" stop-opacity="0.00" />
                    </linearGradient>

                    <!-- Orders Area Gradient -->
                    <linearGradient id="cleanOrdersGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#F68625" stop-opacity="0.30" />
                        <stop offset="65%" stop-color="#F68625" stop-opacity="0.08" />
                        <stop offset="100%" stop-color="#F68625" stop-opacity="0.00" />
                    </linearGradient>

                    <!-- Soft Drop Glow for Active Curves -->
                    <filter id="purpleGlow" x="-10%" y="-10%" width="120%" height="120%">
                        <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#730163" flood-opacity="0.25" />
                    </filter>
                </defs>

                <!-- Horizontal Dashed Reference Grid Lines -->
                <g class="opacity-75 dark:opacity-40">
                    <line
                        v-for="(tick, idx) in yAxisTicks"
                        :key="'grid-' + idx"
                        :x1="paddingLeft - 10"
                        :y1="tick.y"
                        :x2="svgWidth - paddingRight + 10"
                        :y2="tick.y"
                        stroke="#cbd5e1"
                        stroke-dasharray="5,5"
                        stroke-width="1.2"
                    />
                </g>

                <!-- Y-Axis Labels (Crisp, High-Resolution Typography) -->
                <g class="text-[12px] font-mono font-medium fill-slate-400 dark:fill-slate-500">
                    <text
                        v-for="(tick, idx) in yAxisTicks"
                        :key="'lbl-' + idx"
                        :x="paddingLeft - 16"
                        :y="tick.y + 4.5"
                        text-anchor="end"
                    >
                        {{ tick.label }}
                    </text>
                </g>

                <!-- Active Column Vertical Dashed Guide Line -->
                <g v-if="activeIndex !== null && revenuePoints[activeIndex]">
                    <line
                        :x1="revenuePoints[activeIndex].x"
                        :y1="paddingTop"
                        :x2="revenuePoints[activeIndex].x"
                        :y2="baseLineY"
                        stroke="#94a3b8"
                        stroke-width="1.6"
                        stroke-dasharray="4,4"
                        class="transition-all duration-150"
                    />
                </g>

                <!-- Orders Area & Spline Line -->
                <g v-if="viewMode === 'orders' || viewMode === 'combined'">
                    <path
                        :d="ordersAreaPath"
                        fill="url(#cleanOrdersGradient)"
                        class="transition-all duration-300"
                    />
                    <path
                        :d="ordersLinePath"
                        fill="none"
                        stroke="#F68625"
                        stroke-width="3.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="transition-all duration-300"
                    />
                </g>

                <!-- Revenue Area & Spline Line -->
                <g v-if="viewMode === 'revenue' || viewMode === 'combined'">
                    <!-- Area Fill -->
                    <path
                        :d="revenueAreaPath"
                        fill="url(#cleanPurpleGradient)"
                        class="transition-all duration-300"
                    />
                    <!-- Smooth Bold Purple Line (Guaranteed Monotone Clamped) -->
                    <path
                        :d="revenueLinePath"
                        fill="none"
                        stroke="#730163"
                        stroke-width="4.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        filter="url(#purpleGlow)"
                        class="transition-all duration-300"
                    />
                </g>

                <!-- Solid Data Point Dots on Curve -->
                <g v-if="viewMode === 'revenue' || viewMode === 'combined'">
                    <template v-for="(pt, idx) in revenuePoints" :key="'rev-dot-' + idx">
                        <!-- Outer Pulse Halo for Active Point -->
                        <circle
                            v-if="idx === activeIndex"
                            :cx="pt.x"
                            :cy="pt.y"
                            r="13"
                            fill="#730163"
                            opacity="0.18"
                            class="animate-pulse"
                        />
                        <!-- Core Solid Circle -->
                        <circle
                            :cx="pt.x"
                            :cy="pt.y"
                            :r="idx === activeIndex ? 6.5 : 4.5"
                            fill="#730163"
                            stroke="#ffffff"
                            stroke-width="2"
                            class="transition-all duration-150"
                        />
                    </template>
                </g>

                <g v-if="viewMode === 'orders'">
                    <template v-for="(pt, idx) in orderPoints" :key="'ord-dot-' + idx">
                        <circle
                            v-if="idx === activeIndex"
                            :cx="pt.x"
                            :cy="pt.y"
                            r="13"
                            fill="#F68625"
                            opacity="0.2"
                            class="animate-pulse"
                        />
                        <circle
                            :cx="pt.x"
                            :cy="pt.y"
                            :r="idx === activeIndex ? 6.5 : 4.5"
                            fill="#F68625"
                            stroke="#ffffff"
                            stroke-width="2"
                            class="transition-all duration-150"
                        />
                    </template>
                </g>

                <!-- X-Axis Day Labels -->
                <g class="text-[13px] font-bold fill-slate-500 dark:fill-slate-400">
                    <text
                        v-for="(day, idx) in days"
                        :key="'day-' + idx"
                        :x="revenuePoints[idx]?.x || (paddingLeft + idx * (chartInnerWidth / Math.max(days.length - 1, 1)))"
                        :y="baseLineY + 30"
                        text-anchor="middle"
                        :class="{ 'fill-slate-900 dark:fill-white font-black text-[14px]': idx === activeIndex }"
                    >
                        {{ day }}
                    </text>
                </g>

                <!-- Full-Height Invisible Hit-Boxes for Hover Detection -->
                <g>
                    <rect
                        v-for="(day, idx) in days"
                        :key="'slice-' + idx"
                        :x="paddingLeft + idx * (chartInnerWidth / Math.max(days.length - 1, 1)) - (colSliceWidth / 2)"
                        :y="0"
                        :width="colSliceWidth"
                        :height="svgHeight"
                        fill="transparent"
                        class="cursor-pointer"
                        @mouseenter="hoveredIndex = idx"
                    />
                </g>
            </svg>

            <!-- Exact Floating Tooltip Card (Positioned beside active point) -->
            <div
                v-if="activeIndex !== null && revenuePoints[activeIndex]"
                class="absolute pointer-events-none transition-all duration-150 z-30"
                :style="{
                    left: `${(revenuePoints[activeIndex].x / svgWidth) * 100}%`,
                    top: `${(revenuePoints[activeIndex].y / svgHeight) * 100}%`,
                    transform: activeIndex > days.length / 2 
                        ? 'translate(-108%, -50%)' 
                        : 'translate(14px, -50%)'
                }"
            >
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-800/80 shadow-2xl min-w-[200px] space-y-2">
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        {{ days[activeIndex] }}
                    </div>

                    <div v-if="viewMode === 'revenue' || viewMode === 'combined'" class="flex items-center justify-between gap-3 text-xs text-slate-700 dark:text-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#730163] shrink-0" />
                            <span>Revenue:</span>
                        </div>
                        <span class="font-black text-slate-900 dark:text-white font-mono text-sm">
                            ৳{{ (revenue[activeIndex] || 0).toLocaleString() }}
                        </span>
                    </div>

                    <div v-if="viewMode === 'orders' || viewMode === 'combined'" class="flex items-center justify-between gap-3 text-xs text-slate-700 dark:text-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#F68625] shrink-0" />
                            <span>Orders:</span>
                        </div>
                        <span class="font-extrabold text-[#F68625] font-mono text-xs">
                            {{ orders[activeIndex] || 0 }} orders
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
