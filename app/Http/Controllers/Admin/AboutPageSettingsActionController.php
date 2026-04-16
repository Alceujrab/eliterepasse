<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AboutPageSettingsActionController extends Controller
{
    public function upsert(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'about_page_hero_title'    => ['nullable', 'string', 'max:255'],
            'about_page_hero_subtitle' => ['nullable', 'string', 'max:500'],
            'about_page_mission'       => ['nullable', 'string', 'max:2000'],
            'about_page_vision'        => ['nullable', 'string', 'max:2000'],
            'about_page_values'        => ['nullable', 'string', 'max:3000'],
            'about_page_history'       => ['nullable', 'string', 'max:5000'],
            'about_page_history_image' => ['nullable', 'image', 'max:3072'],
            'about_page_video_url'     => ['nullable', 'string', 'max:500'],
            'about_page_stats'         => ['nullable', 'array'],
            'about_page_stats.*.value' => ['nullable', 'string', 'max:50'],
            'about_page_stats.*.label' => ['nullable', 'string', 'max:100'],
            'about_page_team'          => ['nullable', 'array'],
            'about_page_team.*.name'   => ['nullable', 'string', 'max:100'],
            'about_page_team.*.role'   => ['nullable', 'string', 'max:100'],
            'about_page_team.*.bio'    => ['nullable', 'string', 'max:500'],
            'about_page_testimonials'           => ['nullable', 'array'],
            'about_page_testimonials.*.name'    => ['nullable', 'string', 'max:100'],
            'about_page_testimonials.*.role'    => ['nullable', 'string', 'max:100'],
            'about_page_testimonials.*.company' => ['nullable', 'string', 'max:100'],
            'about_page_testimonials.*.text'    => ['nullable', 'string', 'max:2000'],
            'about_page_testimonials.*.video_url' => ['nullable', 'string', 'max:500'],
            'about_page_testimonials.*.rating'  => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $setting = LandingSetting::query()->latest('id')->first();

        $stats = collect($validated['about_page_stats'] ?? [])
            ->map(fn (array $i) => ['value' => trim($i['value'] ?? ''), 'label' => trim($i['label'] ?? '')])
            ->filter(fn (array $i) => $i['value'] !== '' || $i['label'] !== '')
            ->values()->all();

        $team = collect($validated['about_page_team'] ?? [])
            ->map(fn (array $i) => [
                'name'  => trim($i['name'] ?? ''),
                'role'  => trim($i['role'] ?? ''),
                'bio'   => trim($i['bio'] ?? ''),
                'photo' => $i['photo'] ?? ($setting ? collect($setting->about_page_team ?? [])->firstWhere('name', trim($i['name'] ?? ''))['photo'] ?? '' : ''),
            ])
            ->filter(fn (array $i) => $i['name'] !== '')
            ->values()->all();

        // Preserve existing photos for team members
        if ($setting) {
            $existingTeam = collect($setting->about_page_team ?? []);
            foreach ($team as $idx => &$member) {
                if (empty($member['photo'])) {
                    $existing = $existingTeam->get($idx);
                    if ($existing && filled($existing['photo'] ?? '')) {
                        $member['photo'] = $existing['photo'];
                    }
                }
            }
            unset($member);
        }

        $testimonials = collect($validated['about_page_testimonials'] ?? [])
            ->map(fn (array $i) => [
                'name'      => trim($i['name'] ?? ''),
                'role'      => trim($i['role'] ?? ''),
                'company'   => trim($i['company'] ?? ''),
                'text'      => trim($i['text'] ?? ''),
                'video_url' => trim($i['video_url'] ?? ''),
                'rating'    => (int) ($i['rating'] ?? 5),
                'photo'     => '',
            ])
            ->filter(fn (array $i) => $i['name'] !== '')
            ->values()->all();

        // Preserve existing photos for testimonials
        if ($setting) {
            $existingTestimonials = collect($setting->about_page_testimonials ?? []);
            foreach ($testimonials as $idx => &$t) {
                if (empty($t['photo'])) {
                    $existing = $existingTestimonials->get($idx);
                    if ($existing && filled($existing['photo'] ?? '')) {
                        $t['photo'] = $existing['photo'];
                    }
                }
            }
            unset($t);
        }

        $payload = [
            'about_page_hero_title'    => $validated['about_page_hero_title'] ?? null,
            'about_page_hero_subtitle' => $validated['about_page_hero_subtitle'] ?? null,
            'about_page_mission'       => $validated['about_page_mission'] ?? null,
            'about_page_vision'        => $validated['about_page_vision'] ?? null,
            'about_page_values'        => $validated['about_page_values'] ?? null,
            'about_page_history'       => $validated['about_page_history'] ?? null,
            'about_page_video_url'     => $validated['about_page_video_url'] ?? null,
            'about_page_stats'         => $stats,
            'about_page_team'          => $team,
            'about_page_testimonials'  => $testimonials,
        ];

        // Upload history image
        if ($request->hasFile('about_page_history_image')) {
            if ($setting && $setting->about_page_history_image) {
                $old = public_path($setting->about_page_history_image);
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $dir = public_path('uploads/landing');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $file = $request->file('about_page_history_image');
            $name = 'history_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $payload['about_page_history_image'] = 'uploads/landing/' . $name;
        }

        if ($setting) {
            $setting->update($payload);
        } else {
            LandingSetting::create(array_merge(LandingSetting::defaults(), $payload));
        }

        return redirect()->route('admin.v2.about-page.index')
            ->with('admin_success', 'Página Sobre Nós atualizada com sucesso.');
    }

    public function uploadTeamPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
            'index' => ['required', 'integer', 'min:0'],
        ]);

        $dir = public_path('uploads/landing/team');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $request->file('photo');
        $name = 'team_' . $request->index . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);
        $path = 'uploads/landing/team/' . $name;

        // Save to setting
        $setting = LandingSetting::query()->latest('id')->first();
        if ($setting) {
            $team = $setting->about_page_team ?? [];
            if (isset($team[$request->index])) {
                // Delete old photo
                if (filled($team[$request->index]['photo'] ?? '')) {
                    $old = public_path($team[$request->index]['photo']);
                    if (file_exists($old)) {
                        unlink($old);
                    }
                }
                $team[$request->index]['photo'] = $path;
                $setting->update(['about_page_team' => $team]);
            }
        }

        return response()->json(['path' => $path, 'url' => asset($path)]);
    }

    public function uploadTestimonialPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
            'index' => ['required', 'integer', 'min:0'],
        ]);

        $dir = public_path('uploads/landing/testimonials');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $request->file('photo');
        $name = 'testimonial_' . $request->index . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);
        $path = 'uploads/landing/testimonials/' . $name;

        // Save to setting
        $setting = LandingSetting::query()->latest('id')->first();
        if ($setting) {
            $testimonials = $setting->about_page_testimonials ?? [];
            if (isset($testimonials[$request->index])) {
                if (filled($testimonials[$request->index]['photo'] ?? '')) {
                    $old = public_path($testimonials[$request->index]['photo']);
                    if (file_exists($old)) {
                        unlink($old);
                    }
                }
                $testimonials[$request->index]['photo'] = $path;
                $setting->update(['about_page_testimonials' => $testimonials]);
            }
        }

        return response()->json(['path' => $path, 'url' => asset($path)]);
    }

    public function uploadGallery(Request $request): JsonResponse
    {
        $request->validate([
            'photos'   => ['required', 'array'],
            'photos.*' => ['image', 'max:3072'],
        ]);

        $dir = public_path('uploads/landing/gallery');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $setting = LandingSetting::query()->latest('id')->first();
        $gallery = $setting ? ($setting->about_page_gallery ?? []) : [];

        $uploaded = [];
        foreach ($request->file('photos') as $file) {
            $name = 'gallery_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $path = 'uploads/landing/gallery/' . $name;
            $gallery[] = $path;
            $uploaded[] = ['path' => $path, 'url' => asset($path)];
        }

        if ($setting) {
            $setting->update(['about_page_gallery' => array_values($gallery)]);
        } else {
            LandingSetting::create(array_merge(LandingSetting::defaults(), ['about_page_gallery' => $gallery]));
        }

        return response()->json(['uploaded' => $uploaded, 'gallery' => $gallery]);
    }

    public function deleteGallery(Request $request): JsonResponse
    {
        $request->validate(['path' => ['required', 'string']]);

        $setting = LandingSetting::query()->latest('id')->first();
        if (! $setting) {
            return response()->json(['ok' => false], 404);
        }

        $gallery = collect($setting->about_page_gallery ?? []);
        $gallery = $gallery->reject(fn ($g) => $g === $request->path)->values()->all();

        $fullPath = public_path($request->path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $setting->update(['about_page_gallery' => $gallery]);

        return response()->json(['ok' => true]);
    }
}
