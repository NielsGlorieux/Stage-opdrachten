@extends('layouts.app')

@section('content')
@include('partials.inbox')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Outbox</div>
                <?php $messages = $sendbox; ?>
                @include('partials.outMessages')
                <div class="text-center">
                    <nav>
                        <?php echo $sendbox->links() ?>                       
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
