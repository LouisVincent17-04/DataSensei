{{--
    Sidebar picker.

    Pages that any role can open (the profile page, the shared module library)
    used to hard-code one sidebar, so an instructor or an admin was shown the
    student navigation. This partial renders the navigation that belongs to the
    signed-in user, so the chrome always matches the account.
--}}
@php
    $roleSidebarUser = auth()->user();
    $roleSidebarRole = (int) ($roleSidebarUser->role ?? \App\Models\User::ROLE_USER);
@endphp

@switch($roleSidebarRole)
    @case(\App\Models\User::ROLE_SUPERADMIN)
        @include('partials.superadmin-sidebar')
        @break

    @case(\App\Models\User::ROLE_ADMIN)
        @include('partials.admin-sidebar')
        @break

    @case(\App\Models\User::ROLE_INSTITUTION_ADMIN)
        @include('partials.institution-admin-sidebar')
        @break

    @case(\App\Models\User::ROLE_INSTRUCTOR)
        @include('partials.instructor-sidebar')
        @break

    @default
        @include('partials.sidebar')
@endswitch
