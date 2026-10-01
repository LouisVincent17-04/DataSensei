@extends('admin.layout')

@section('title', $result->title.' Report')
@section('page_title', 'Reports')
@section('page_subtitle', 'System-wide reports built from DataSensei\'s saved records. Pick a report, narrow it with the filters, then print it or export it as PDF or CSV.')

@section('content')
  @include('reports.partials.report', [
    'showRoute' => 'admin.reports.show',
    'exportRoute' => 'admin.reports.export',
  ])
@endsection
