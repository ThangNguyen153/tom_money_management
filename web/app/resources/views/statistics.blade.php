<!DOCTYPE html>
<html>
<head>
    <title>Statistic</title>
</head>
<body>

<button><a href="{{ route('logout') }}">Log out</a></button>
<button><a href="{{ route('user-daily-usage') }}">Daily Usage</a></button>
<button><a href="{{ route('user-usage-statistics') }}">Statistic</a></button>
<button><a href="{{ route('user-activity-log') }}">Activity Log</a></button>

<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="panel panel-default">
                <div class="panel-heading">Statistic</div>
                <div class="panel-body">
                    <canvas id="canvas" height="280" width="600"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script>
    var labels = <?php echo $labels; ?>;
    var usages = <?php echo $usages; ?>;
    var barChartData = {
        labels: labels,
        datasets: [{
            label: 'Usage Type',
            backgroundColor: "pink",
            data: usages
        }]
    };

    window.onload = function() {
        var ctx = document.getElementById("canvas").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: '#c1c1c1',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'Total Usage Of Month'
                }
            }
        });
    };
</script>

</body>
</html>
