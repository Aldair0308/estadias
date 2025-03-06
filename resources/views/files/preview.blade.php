@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4">{{ $file->original_name }}</h2>
        
        @if(isset($htmlContent))
            {{-- Word Document Preview --}}
            <div class="prose max-w-none">
                {!! $htmlContent !!}
            </div>
        @elseif(isset($pdfUrl))
            {{-- PDF Preview --}}
            <div class="w-full h-screen">
                <embed src="{{ $pdfUrl }}" type="application/pdf" width="100%" height="100%">
            </div>
        @elseif($file->isExcel())
            {{-- Excel Preview --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    @foreach($excelPreview ?? [] as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border">
                                    {{ $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <p class="text-gray-600">Preview not available for this file type.</p>
        @endif
    </div>
</div>
@endsection