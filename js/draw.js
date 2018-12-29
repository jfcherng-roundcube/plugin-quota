function drawDiskQuota() {
  var ctx = document.getElementById('chartContainer').getContext('2d');

  var colors = {
    blue: 'rgb(54, 162, 235)',
    green: 'rgb(75, 192, 192)',
    grey: 'rgb(201, 203, 207)',
    orange: 'rgb(255, 159, 64)',
    purple: 'rgb(153, 102, 255)',
    red: 'rgb(255, 99, 132)',
    yellow: 'rgb(255, 205, 86)'
  };

  var config = {
    type: 'pie',
    data: {
      datasets: [{
        data: [
          plugin_quota_chart_vars.quotaUsedKb,
          plugin_quota_chart_vars.quotaFreeKb
        ],
        backgroundColor: [
          colors.red,
          colors.green
        ],
        label: plugin_quota_chart_vars.charTitle
      }],
      labels: [
        plugin_quota_chart_vars.labelUsedSpace,
        plugin_quota_chart_vars.labelFreeSpace
      ]
    },
    options: {
      responsive: true,
      tooltips: {
        enabled: true,
        callbacks: {
          label: function(tooltipItem, data) {
            // console.log(tooltipItem, data);

            var title = data.labels[tooltipItem.index];

            var spaceHumanized = tooltipItem.index === 0
              ? plugin_quota_chart_vars.quotaUsedHumanized
              : plugin_quota_chart_vars.quotaFreeHumanized;

            var spaceKb = tooltipItem.index === 0
              ? plugin_quota_chart_vars.quotaUsedKb
              : plugin_quota_chart_vars.quotaFreeKb;

            var spacePercentage = spaceKb / plugin_quota_chart_vars.quotaTotalKb * 100;

            return title + ' ' + spaceHumanized + ' ( ' + spacePercentage.toFixed(2) + '% )';
          }
        }
      }
    }
  };

  window.myPieChart = new Chart(ctx, config);
}
