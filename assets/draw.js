const Chart = global.Chart;

const colors = {
  blue: 'rgb(54, 162, 235)',
  green: 'rgb(75, 192, 192)',
  grey: 'rgb(201, 203, 207)',
  orange: 'rgb(255, 159, 64)',
  purple: 'rgb(153, 102, 255)',
  red: 'rgb(255, 99, 132)',
  yellow: 'rgb(255, 205, 86)',
};

global.drawDiskQuota = () => {
  const plugin_quota_chart_vars = global.plugin_quota_chart_vars;

  let ctx = document.getElementById('chart-container').getContext('2d');

  let config = {
    type: 'pie',
    data: {
      datasets: [
        {
          data: [plugin_quota_chart_vars.quota_used_kb, plugin_quota_chart_vars.quota_free_kb],
          backgroundColor: [colors.red, colors.green],
          label: plugin_quota_chart_vars.char_title,
        },
      ],
      labels: [plugin_quota_chart_vars.label_used_space, plugin_quota_chart_vars.label_free_space],
    },
    options: {
      responsive: true,
      tooltips: {
        enabled: true,
        callbacks: {
          label: (tooltip_item, data) => {
            // console.log(tooltip_item, data);

            let title = data.labels[tooltip_item.index];

            let space_humanized =
              tooltip_item.index === 0
                ? plugin_quota_chart_vars.quota_used_humanized
                : plugin_quota_chart_vars.quota_free_humanized;

            let space_kb =
              tooltip_item.index === 0 ? plugin_quota_chart_vars.quota_used_kb : plugin_quota_chart_vars.quota_free_kb;

            let space_percentage = (space_kb / plugin_quota_chart_vars.quota_total_kb) * 100;

            return `${title} ${space_humanized} ( ${space_percentage.toFixed(2)}% )`;
          },
        },
      },
    },
  };

  global.myPieChart = new Chart(ctx, config);
};
