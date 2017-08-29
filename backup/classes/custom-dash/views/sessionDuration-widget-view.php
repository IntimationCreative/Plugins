<canvas id="sessionDuration" width="400" height="400" style="display: block; width: 400px; height: 400px; margin-bottom: 2rem;"></canvas>

<!-- Google Analytics Reporting API v4 Implementation -->
<script>
    var values = <?php print_r( $this->results ); ?>;
    var timeFormat = "MMM DD";
        
    function newDateString(days) {
        return moment().subtract(days, 'd');
    }
    var dates = [];
    
    for (var i = 30; i > -1; i--) {
        dates[i] = newDateString(i);
    }
    var sessionDurationconfig = {
        type: 'scatter',
        data: {
            labels: dates,
            datasets: [{
                label: "Session Duration By Day",
                backgroundColor: 'rgb(165, 165, 163)',
                borderColor: 'rgb(165, 165, 163)',
                fill: false,
                data: values,
            }]
        },
        options: {
            title:{
                text: "Chart.js Time Scale"
            },
            scales: {
                xAxes: [{
                    type: "time",
                    time: {
                        format: timeFormat,
                        round: 'day',
                        tooltipFormat: 'll'
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Date'
                    }
                }, ],
                yAxes: [{
                    scaleLabel: {
                        display: false,
                        labelString: 'value'
                    }
                }]
            },
        }
    };
    
    var sessionDurationctx = document.getElementById("sessionDuration").getContext("2d");
    window.myLine = new Chart(sessionDurationctx, sessionDurationconfig);

</script>
