<div class="col-12 col-md-6 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6 style="font-weight: bold">{{ trans('dashboard.net_earnings') }}</h6>
            <div class="card-extra">
                <ul class="card-tab-links nav-tabs nav" role="tablist">
                    <li>
                        <a href="#net-sales-today" data-bs-toggle="tab" id="net-sales-today-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.today') }}</a>
                    </li>
                    <li>
                        <a href="#net-sales-week" data-bs-toggle="tab" id="net-sales-week-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.week') }}</a>
                    </li>
                    <li>
                        <a href="#net-sales-month" data-bs-toggle="tab" id="net-sales-month-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.month') }}</a>
                    </li>
                    <li>
                        <a href="#net-sales-year" data-bs-toggle="tab" id="net-sales-year-tab"
                            role="tab" aria-selected="true" class="active" style="font-size: 12px">{{ trans('dashboard.year') }}</a>
                    </li>
                    <li>
                        <a href="#net-sales-5years" data-bs-toggle="tab" id="net-sales-5years-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">5 {{ trans('dashboard.years') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0 pb-25">
            <div class="tab-content">
                <div class="tab-pane" id="net-sales-today" role="tabpanel"
                    aria-labelledby="net-sales-today-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="netSalesToday"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="net-sales-week" role="tabpanel"
                    aria-labelledby="net-sales-week-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="netSalesWeek"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="net-sales-month" role="tabpanel"
                    aria-labelledby="net-sales-month-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="netSalesMonth"></canvas>
                    </div>
                </div>
                <div class="tab-pane active show" id="net-sales-year" role="tabpanel"
                    aria-labelledby="net-sales-year-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="netSalesYear"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="net-sales-5years" role="tabpanel"
                    aria-labelledby="net-sales-5years-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="netSales5Years"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
