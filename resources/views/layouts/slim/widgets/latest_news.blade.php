<div id="accordion3" class="accordion-two" role="tablist" aria-multiselectable="true">
    @foreach($news as $item)
        <div class="card">
            <div class="card-header" role="tab" id="headingOne3-{{ $item->id }}">
                <a class="collapsed tx-gray-800 transition" data-toggle="collapse" data-parent="#accordion3" href="#collapseOne3-{{ $item->id }}" aria-expanded="false" aria-controls="collapseOne3-{{ $item->id }}">
                    {{ $item->subject }}
                </a>
            </div>
            <div id="collapseOne3-{{ $item->id }}" class="collapse" role="tabpanel" aria-labelledby="headingOne3-{{ $item->id }}">
                <div class="card-body">
                    <h6>{{ $item->user->name }} - {{ show_datetime($item->created_at) }}</h6>
                    <br />
                    {{ $item->body }}
                </div>
            </div>
        </div>
    @endforeach
</div><!-- accordion -->