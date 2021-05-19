//Check if SSE is supported
if(typeof(EventSource) !== "undefined") {
    //Try to check that data-saver is not on
    if (getMeta('save_data') !== 'true' && (('connection' in navigator) === false || navigator.connection.saveData !== true)) {
        //Cron processing
        let cronSource = new EventSource('/api/cron/');
        //Log tasks' statuses
        ['CronTaskStart', 'CronTaskEnd', 'CronTaskFail'].forEach(function(event) {
            cronSource.addEventListener(event, function(event) {
                console.log(event.data);
            });
        });
    }
}

