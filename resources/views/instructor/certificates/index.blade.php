@extends('instructor.layout')

@use('App\Support\Certificates\CertificateLayouts')
@use('App\Support\Glossary')

@section('title', 'Certificate Builder')
@section('page_title', 'Certificate Builder')
@section('page_subtitle', 'Certificates of Completion for your classes. Each one stands for all the work you assigned to a class in the semester: its modules, activities and assessments. Choose one of five layouts, then at the end of the semester issue it to the students who completed the class.')

@section('page_actions')
  @if($hasClasses)
    <a class="ct-btn" href="{{ route('instructor.certificates.create') }}">New certificate</a>
  @endif
@endsection

@section('content')
  @include('certificates._styles')

  <section class="ct-section">
    <h2>Your certificates</h2>
    @if($definitions->isEmpty())
      <div class="ct-empty">
        @if($hasClasses)
          No certificates yet. Create a Certificate of Completion for one of your classes.
        @else
          Create a class first; a certificate always belongs to one of your classes.
        @endif
      </div>
    @else
      <div class="ct-table-wrap">
        <table class="ct-table">
          <thead>
            <tr>
              <th scope="col">Certificate</th>
              <th scope="col">Course / class</th>
              <th scope="col">Semester</th>
              <th scope="col">{!! Glossary::help('certificate_layout') !!}</th>
              <th scope="col">{!! Glossary::help('certificate_status') !!}</th>
              <th scope="col">Issued</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($definitions as $definition)
              <tr>
                <td>{{ $definition->name }}</td>
                <td>{{ trim(($definition->classRoom?->subject_code ? $definition->classRoom->subject_code.' ' : '').$definition->classRoom?->name) }}{{ $definition->classRoom?->section ? ', '.$definition->classRoom->section : '' }}@if($definition->classRoom?->is_archived) <span class="ct-muted">(archived)</span>@endif</td>
                <td>{{ $definition->classRoom?->term ?: ($definition->classRoom?->academic_year ?: '—') }}</td>
                <td>{{ CertificateLayouts::name($definition->layout_key) }}</td>
                <td><span class="{{ ['active' => 'ct-good', 'inactive' => 'ct-muted', 'draft' => 'ct-warn'][$definition->status] ?? '' }}">{{ $definition->statusLabel() }}</span></td>
                <td>{{ $definition->issued_count }}</td>
                <td class="ct-actions">
                  <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.show', $definition) }}">Review and issue</a>
                  <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.edit', $definition) }}">Edit</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
@endsection
