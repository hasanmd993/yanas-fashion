<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Services\ImageService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('sort_order', 'asc')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tag' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:12288', // up to 12MB banner
            'button_text' => 'required|string|max:255',
            'button_link' => 'required|string|max:255',
            'secondary_button_text' => 'nullable|string|max:255',
            'secondary_button_link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = ImageService::uploadAndOptimize($request->file('image_file'), 'sliders', 1920, 85);
        } elseif (empty($validated['image'])) {
            $validated['image'] = 'assets/hero.jpg';
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Slider::create($validated);

        return redirect()->route('admin.sliders.index')->with('success', 'Hero slide added successfully.');
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tag' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:12288',
            'button_text' => 'required|string|max:255',
            'button_link' => 'required|string|max:255',
            'secondary_button_text' => 'nullable|string|max:255',
            'secondary_button_link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            ImageService::delete($slider->image);
            $validated['image'] = ImageService::uploadAndOptimize($request->file('image_file'), 'sliders', 1920, 85);
        } elseif (empty($validated['image'])) {
            $validated['image'] = $slider->image;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $slider->update($validated);

        return redirect()->route('admin.sliders.index')->with('success', 'Hero slide updated successfully.');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        ImageService::delete($slider->image);
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Hero slide deleted successfully.');
    }
}
