<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $module->title }}</title>
    <style>
        @page { margin: 2cm; }
        body { color: #1e293b; font-family: DejaVu Sans, sans-serif; font-size: 11pt; line-height: 1.65; }
        h1 { color: #0f172a; font-size: 22pt; margin: 0 0 8pt; }
        h2, h3, h4 { color: #1e293b; margin: 18pt 0 8pt; }
        .course { color: #64748b; font-size: 9pt; margin-bottom: 18pt; }
        .description { border-left: 3px solid #f6863b; color: #475569; margin: 0 0 20pt; padding: 4pt 0 4pt 12pt; }
        .content { overflow-wrap: anywhere; }
        .content img { max-width: 100%; height: auto; }
        .content table { border-collapse: collapse; width: 100%; }
        .content th, .content td { border: 1px solid #cbd5e1; padding: 6pt; }
        .content th { background: #fff7ed; }
        .content pre { background: #f1f5f9; padding: 10pt; white-space: pre-wrap; }
        .content blockquote { border-left: 3px solid #f6863b; color: #475569; margin-left: 0; padding-left: 12pt; }
        .question { border-top: 1px solid #cbd5e1; margin-top: 22pt; padding-top: 12pt; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>{{ $module->title }}</h1>
    @if($module->course)
        <div class="course">{{ $module->course->title }}</div>
    @endif

    @if($module->description)
        <div class="description">{{ $module->description }}</div>
    @endif

    @if($module->content)
        <div class="content">{!! $module->content !!}</div>
    @endif

    @if($module->question && in_array($module->type, ['assignment', 'quiz', 'coding']))
        <div class="question">
            <h2>Soal / Instruksi</h2>
            {{ $module->question }}
        </div>
    @endif
</body>
</html>
