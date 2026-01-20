@if(isset($stats['refunds']))
<div class="col-12 col-md-6 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6 style="font-weight: bold">{{ trans('dashboard.refunds') }}</h6>
            <div class="card-extra">
                <ul class="card-tab-links nav-tabs nav" role="tablist">
                    <li>
                        <a href="#refunds-today" data-bs-toggle="tab" id="refunds-today-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.today') }}</a>
                    </li>
                    <li>
                        <a href="#refunds-week" data-bs-toggle="tab" id="refunds-week-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.week') }}</a>
                    </li>
                    <li>
                        <a href="#refunds-month" data-bs-toggle="tab" id="refunds-month-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.month') }}</a>
                    </li>
                    <li>
                        <a href="#refunds-year" data-bs-toggle="tab" id="refunds-year-tab"
                            role="tab" aria-selected="true" class="active" style="font-size: 12px">{{ trans('dashboard.year') }}</a>
                    </li>
                    <li>
                        <a href="#refunds-5years" data-bs-toggle="tab" id="refunds-5years-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">5 {{ trans('dashboard.years') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0 pb-25">
            <div class="tab-content">
                <div class="tab-pane" id="refunds-today" role="tabpanel"
                    aria-labelledby="refunds-today-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="refundsToday"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="refunds-week" role="tabpanel"
                    aria-labelledby="refunds-week-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="refundsWeek"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="refunds-month" role="tabpanel"
                    aria-labelledby="refunds-month-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="refundsMonth"></canvas>
                    </div>
                </div>
                <div class="tab-pane active show" id="refunds-year" role="tabpanel"
                    aria-labelledby="refunds-year-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="refundsYear"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="refunds-5years" role="tabpanel"
                    aria-labelledby="refunds-5years-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="refunds5Years"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
