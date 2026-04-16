<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingBannerActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
        ]);

        $path = $request->file('image')->store('landing/banners', 'public');
        $maxOrder = LandingBanner::max('order') ?? 0;

        LandingBanner::create([
            'image_path' => $path,
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.v2.landing-banners.index')
            ->with('admin_success', 'Banner adicionado com sucesso.');
    }

    public function update(Request $request, LandingBanner $banner): RedirectResponse
    {
        $request->validate([
            'image' => 'nullable|image|max:4096',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'is_active' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $banner->image_path = $request->file('image')->store('landing/banners', 'public');
        }

        $banner->title = $request->input('title');
        $banner->subtitle = $request->input('subtitle');
        $banner->is_active = $request->boolean('is_active');
        $banner->save();

        return redirect()->route('admin.v2.landing-banners.index')
            ->with('admin_success', 'Banner atualizado com sucesso.');
    }

    public function destroy(LandingBanner $banner): RedirectResponse
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.v2.landing-banners.index')
            ->with('admin_success', 'Banner removido com sucesso.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:landing_banners,id',
        ]);

        foreach ($request->input('order') as $position => $id) {
            LandingBanner::where('id', $id)->update(['order' => $position + 1]);
        }

        return redirect()->route('admin.v2.landing-banners.index')
            ->with('admin_success', 'Ordem dos banners atualizada.');
    }
}
