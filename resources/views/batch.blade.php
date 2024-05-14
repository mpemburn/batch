@extends('layouts.app')

@section('content')
    <pre>
        <?php
        $batch->getBatchFile($batchFile)
            ->run();
        ?>
    </pre>
@endsection

