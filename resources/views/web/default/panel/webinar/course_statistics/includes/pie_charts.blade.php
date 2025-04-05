<div class="col-12 col-md-3 mt-20">
    <div class="course-statistic-cards-shadow pt-15 px-15 pb-25 rounded-sm bg-white">
        <span class="d-block font-16 font-weight-bold text-secondary">{{ $cardTitle }}</span>
        <div class="mt-25 statistic-pie-charts">
            <canvas id="{{ $cardId }}" height="197"></canvas>
        </div>

        <div class="mt-25">
            <div class="d-flex align-items-center">
                <span class="cart-label-color rounded-circle bg-primary mr-5"></span>
                <span class="font-14 font-weight-500 text-gray">{{ $cardPrimaryLabel }}</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="cart-label-color rounded-circle bg-secondary mr-5"></span>
                <span class="font-14 font-weight-500 text-gray">{{ $cardSecondaryLabel }}</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="cart-label-color rounded-circle bg-warning mr-5"></span>
                <span class="font-14 font-weight-500 text-gray">{{ $cardWarningLabel }}</span>
            </div>
        </div>
    </div>
</div>
