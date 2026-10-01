@extends('instructor.layout')

@section('title', $result->title.' Report')
@section('page_title', 'Reports')
@section('page_subtitle', 'Reports on the classes you teach and their students. Pick a report, narrow it with the filters, then print it or export it as PDF or CSV.')

@section('content')
  @if($noClasses)
    <div class="rp-panel" style="padding:20px">You have no classes yet. Create a class and add students to see reports here.</div>
  @endif
  @include('reports.partials.report', [
    'showRoute' => 'instructor.reports.show',
    'exportRoute' => 'instructor.reports.export',
  ])
@endsection
