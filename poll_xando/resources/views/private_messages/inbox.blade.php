@extends('layouts.app')

@section('content')
@include('partials.inbox')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Inbox</div>
                <?php $messages = $inbox; ?>
                @include('partials.inMessages')
                <div class="text-center">
                    <nav>
                        <?php echo $inbox->links() ?>                       
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
