<?php

namespace App\Http\Controllers;

use App\Models\ChallengeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
    public function index()
    {
        $categories = ChallengeCategory::orderBy('order_index', 'asc')->get();
        
        // Check if the user is logged in AND has an organization assigned
        $hasUniversity = Auth::check() && !empty(Auth::user()->organization_id);

        if ($categories->isEmpty()) {
            $categories = collect([
                (object)[
                    'name' => 'Newbie',
                    'slug' => 'newbie',
                    'target_audience' => 'Just starting, little to no background in data',
                    'description' => 'Learn the absolute basics of Python and data literacy from scratch. No prior coding experience required.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>'
                ],
                (object)[
                    'name' => 'University Student',
                    'slug' => 'university-student',
                    'target_audience' => 'Currently studying Data Science, CS, or related fields',
                    'description' => 'Bridge the gap between academic theory and practical application. Focus on algorithms, stats, and real coding.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>'
                ],
                (object)[
                    'name' => 'Intermediate',
                    'slug' => 'intermediate',
                    'target_audience' => 'Has basics (Python, stats) and can do small projects',
                    'description' => 'Level up your skills. Dive into data wrangling, machine learning fundamentals, and visualization techniques.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>'
                ],
                (object)[
                    'name' => 'Advanced',
                    'slug' => 'advanced',
                    'target_audience' => 'Strong skills, can build models and real-world systems',
                    'description' => 'Tackle complex datasets, deep learning, optimization, and scalable data pipelines.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" /><line stroke-linecap="round" stroke-linejoin="round" x1="8.59" y1="13.51" x2="15.42" y2="17.49" /><line stroke-linecap="round" stroke-linejoin="round" x1="15.41" y1="6.51" x2="8.59" y2="10.49" /></svg>'
                ],
                (object)[
                    'name' => 'Professional',
                    'slug' => 'professional',
                    'target_audience' => 'Working in industry (Data Analyst, Data Scientist, ML Engineer)',
                    'description' => 'Master MLOps, system architecture, big data frameworks, and high-level strategy for enterprise systems.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7.52a2 2 0 00-1-1.73l-6-3.46a2 2 0 00-2 0l-6 3.46a2 2 0 00-1 1.73v6.93a2 2 0 001 1.73l6 3.46a2 2 0 002 0l6-3.46a2 2 0 001-1.73V7.52z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 22.5V12M12 12L2.5 7M12 12l9.5-5" /></svg>'
                ]
            ]);
        }

        return view('student.challenges', compact('categories', 'hasUniversity'));
    }

    public function map($slug)
    {
        return view('student.challenges-map', compact('slug')); 
    }

    // NEW METHOD TO PROCESS THE MODAL SUBMISSION
    public function enrollOrganization(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string',
        ]);

        // Look up the organization by the submitted invite code
        $org = DB::table('organizations')->where('invite_code', $request->invite_code)->first();

        if ($org) {
            // Valid code! Assign the organization_id to the user
            $user = Auth::user();
            $user->organization_id = $org->id;
            $user->save();

            return back()->with('success', 'Successfully enrolled in ' . $org->name . '! Your university path is now unlocked.');
        }

        // Invalid code
        return back()->withErrors(['invite_code' => 'Invalid invite code. Please check and try again.']);
    }
}