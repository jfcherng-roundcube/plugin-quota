function drawDiskQuota() {

    var quotaUsedPercents = document.getElementById('quotaUsedPercents').innerHTML;
    var quotaFreePercents = document.getElementById('quotaFreePercents').innerHTML;
    var labelUsedSpace = document.getElementById('labelUsedSpace').innerHTML;
    var labelFreeSpace = document.getElementById('labelFreeSpace').innerHTML;

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
            text: plugin_quota_chartTitle
        },
        data: [{
            type: "pie",
            startAngle: 275,
            yValueFormatString: "##0.00\"%\"",
            indexLabel: "{label} {y}",
            dataPoints: [
                {y: quotaUsedPercents, label: labelUsedSpace, color: "rgb(3,71,91)"},
                {y: quotaFreePercents, label: labelFreeSpace, color: "rgb(199,227,239)"}
            ]
        }]
    });

    chart.render();

}
