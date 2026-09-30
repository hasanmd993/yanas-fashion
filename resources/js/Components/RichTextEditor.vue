<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import {
    Bold,
    Italic,
    Underline as UnderlineIcon,
    Strikethrough,
    Heading2,
    Heading3,
    List,
    ListOrdered,
    Quote,
    Minus,
    Undo,
    Redo,
    Link2,
    Link2Off,
    Code,
    RemoveFormatting,
    CodeXml
} from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Write product description...',
    },
});

const emit = defineEmits(['update:modelValue']);

const showHtmlSource = ref(false);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [2, 3],
            },
        }),
        Underline,
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-[#730163] dark:text-purple-400 underline font-semibold',
                target: '_blank',
            },
        }),
    ],
    editorProps: {
        attributes: {
            class: 'prose dark:prose-invert max-w-none min-h-[160px] p-4 focus:outline-none text-xs text-slate-800 dark:text-slate-200 leading-relaxed',
        },
    },
    onUpdate: () => {
        emit('update:modelValue', editor.value.getHTML());
    },
});

// Sync external modelValue changes
watch(
    () => props.modelValue,
    (value) => {
        const isSame = editor.value?.getHTML() === value;
        if (!isSame && editor.value) {
            editor.value.commands.setContent(value || '', false);
        }
    }
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});

const setLink = () => {
    const previousUrl = editor.value.getAttributes('link').href;
    const url = window.prompt('Enter URL:', previousUrl || 'https://');
    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

const handleRawHtmlInput = (e) => {
    const val = e.target.value;
    emit('update:modelValue', val);
    editor.value?.commands.setContent(val || '', false);
};
</script>

<template>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 overflow-hidden shadow-sm transition-all focus-within:border-[#730163] focus-within:ring-2 focus-within:ring-[#730163]/10">
        <!-- Editor Toolbar -->
        <div class="p-2 bg-slate-100/90 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center gap-1">
            <!-- Text Formatting Group -->
            <button
                type="button"
                @click="editor?.chain().focus().toggleBold().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('bold') }"
                title="Bold"
            >
                <Bold class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().toggleItalic().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('italic') }"
                title="Italic"
            >
                <Italic class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().toggleUnderline().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('underline') }"
                title="Underline"
            >
                <UnderlineIcon class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().toggleStrike().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('strike') }"
                title="Strikethrough"
            >
                <Strikethrough class="w-3.5 h-3.5" />
            </button>

            <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-1" />

            <!-- Headings -->
            <button
                type="button"
                @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40 text-xs font-bold"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('heading', { level: 2 }) }"
                title="Heading 2"
            >
                <Heading2 class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40 text-xs font-bold"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('heading', { level: 3 }) }"
                title="Heading 3"
            >
                <Heading3 class="w-3.5 h-3.5" />
            </button>

            <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-1" />

            <!-- Lists -->
            <button
                type="button"
                @click="editor?.chain().focus().toggleBulletList().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('bulletList') }"
                title="Bullet List"
            >
                <List class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().toggleOrderedList().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('orderedList') }"
                title="Numbered List"
            >
                <ListOrdered class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().toggleBlockquote().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('blockquote') }"
                title="Blockquote"
            >
                <Quote class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().setHorizontalRule().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                title="Divider Line"
            >
                <Minus class="w-3.5 h-3.5" />
            </button>

            <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-1" />

            <!-- Link -->
            <button
                type="button"
                @click="setLink"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                :class="{ 'bg-[#730163] text-white hover:bg-[#730163]': editor?.isActive('link') }"
                title="Add Link"
            >
                <Link2 class="w-3.5 h-3.5" />
            </button>

            <button
                v-if="editor?.isActive('link')"
                type="button"
                @click="editor?.chain().focus().unsetLink().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                title="Remove Link"
            >
                <Link2Off class="w-3.5 h-3.5" />
            </button>

            <!-- Clear formatting -->
            <button
                type="button"
                @click="editor?.chain().focus().clearNodes().unsetAllMarks().run()"
                :disabled="!editor || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-40"
                title="Clear Formatting"
            >
                <RemoveFormatting class="w-3.5 h-3.5" />
            </button>

            <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-1" />

            <!-- Undo / Redo -->
            <button
                type="button"
                @click="editor?.chain().focus().undo().run()"
                :disabled="!editor?.can().undo() || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-30"
                title="Undo"
            >
                <Undo class="w-3.5 h-3.5" />
            </button>

            <button
                type="button"
                @click="editor?.chain().focus().redo().run()"
                :disabled="!editor?.can().redo() || showHtmlSource"
                class="p-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-30"
                title="Redo"
            >
                <Redo class="w-3.5 h-3.5" />
            </button>

            <!-- Toggle Raw HTML Source -->
            <div class="ml-auto">
                <button
                    type="button"
                    @click="showHtmlSource = !showHtmlSource"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold flex items-center gap-1.5 border transition-colors"
                    :class="showHtmlSource 
                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900' 
                        : 'bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600 hover:bg-slate-50'"
                    title="View HTML Source Code"
                >
                    <CodeXml class="w-3.5 h-3.5" />
                    <span>{{ showHtmlSource ? 'WYSIWYG' : 'HTML' }}</span>
                </button>
            </div>
        </div>

        <!-- Visual Editor Area -->
        <div v-show="!showHtmlSource" class="bg-white dark:bg-slate-900 min-h-[160px] cursor-text" @click="editor?.commands.focus()">
            <EditorContent :editor="editor" />
        </div>

        <!-- Raw HTML Source Editor -->
        <div v-show="showHtmlSource" class="bg-slate-950 p-3">
            <textarea
                :value="modelValue"
                @input="handleRawHtmlInput"
                rows="7"
                class="w-full bg-transparent font-mono text-xs text-emerald-400 focus:outline-none resize-y"
                placeholder="<p>Raw HTML content...</p>"
            />
        </div>
    </div>
</template>

<style>
/* Prose Mirror Editor Styling */
.ProseMirror {
    min-height: 140px;
}
.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #94a3b8;
    pointer-events: none;
    height: 0;
}
.ProseMirror h2 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-top: 0.8rem;
    margin-bottom: 0.4rem;
}
.ProseMirror h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-top: 0.6rem;
    margin-bottom: 0.3rem;
}
.ProseMirror ul {
    list-style-type: disc;
    padding-left: 1.25rem;
    margin: 0.5rem 0;
}
.ProseMirror ol {
    list-style-type: decimal;
    padding-left: 1.25rem;
    margin: 0.5rem 0;
}
.ProseMirror blockquote {
    border-left: 3px solid #730163;
    padding-left: 0.75rem;
    margin: 0.5rem 0;
    font-style: italic;
    color: #64748b;
}
.ProseMirror hr {
    border: none;
    border-top: 1px solid #e2e8f0;
    margin: 1rem 0;
}
</style>
