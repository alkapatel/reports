$("#date_range_picker").ionRangeSlider({
    type: "single",
    min: 0,
    max: 100,
    grid: true,            
    grid_num: 10,
    step: 1,
    from: startRangeFrom,
    prettify_enabled: false,
    onFinish: function (data) 
    {
        $('#task_progress').val(data.from);
    }
});            
