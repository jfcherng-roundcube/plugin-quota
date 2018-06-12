function drawDiskQuota() {
  var quota_chart = echarts.init(document.getElementById('chartContainer'));

  quota_chart.setOption({
    title: {
      text: plugin_quota_chart_vars.chartTitle,
      x: 'center'
    },
    tooltip: {
      trigger: 'item',
      formatter: "{b} : {c} KB ({d}%)"
    },
    legend: {
      orient: 'vertical',
      left: 'right',
      data: [
        plugin_quota_chart_vars.labelUsedSpace,
        plugin_quota_chart_vars.labelFreeSpace
      ]
    },
    series: [{
      name: 'SeriesName',
      type: 'pie',
      radius: '70%',
      center: ['45%', '50%'],
      data: [{
        value: plugin_quota_chart_vars.quotaUsedKb,
        name: plugin_quota_chart_vars.labelUsedSpace
      }, {
        value: plugin_quota_chart_vars.quotaFreeKb,
        name: plugin_quota_chart_vars.labelFreeSpace
      }],
      itemStyle: {
        emphasis: {
          shadowBlur: 10,
          shadowOffsetX: 0,
          shadowColor: 'rgba(0, 0, 0, 0.5)'
        }
      }
    }]
  });

  // responsive when the window get resized
  window.addEventListener("resize", function() {
    quota_chart.resize();
  });
}
