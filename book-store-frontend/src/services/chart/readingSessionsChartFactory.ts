import type { ChartOptions } from 'chart.js'
import type { AppChartConfig } from '@/types/chart'

type ReadingSession = {
  date: string
  sessions: number
  pages_read: number
  duration_seconds: number
}

export class ReadingSessionsChartFactory {
  public static make(data: ReadingSession[]): AppChartConfig<'line'> {
    const labels = data.map((i) => i.date)

    return {
      type: 'line',
      data: {
        labels,
        datasets: [this.sessionsDataset(data), this.pagesDataset(data), this.timeDataset(data)],
      },
      options: this.options(),
    }
  }

  private static sessionsDataset(data: ReadingSession[]) {
    return {
      label: 'Sessions',
      data: data.map((i) => i.sessions),
      borderColor: '#5B9CF6',
      backgroundColor: 'rgba(91, 156, 246, 0.10)',
      tension: 0.4,
      fill: true,
      yAxisID: 'y',
    }
  }

  private static pagesDataset(data: ReadingSession[]) {
    return {
      label: 'Pages',
      data: data.map((i) => i.pages_read),
      borderColor: '#3ECF8E',
      backgroundColor: 'rgba(62, 207, 142, 0.10)',
      tension: 0.4,
      fill: true,
      yAxisID: 'y',
    }
  }

  private static timeDataset(data: ReadingSession[]) {
    return {
      label: 'Reading Time',
      data: data.map((i) => i.duration_seconds),
      borderColor: '#E8A020',
      backgroundColor: 'rgba(232, 160, 32, 0.18)',
      tension: 0.4,
      fill: true,
      yAxisID: 'y1',
    }
  }

  private static options() {
    return {
      responsive: true,
      maintainAspectRatio: false,

      interaction: {
        mode: 'index',
        intersect: false,
      },

      elements: {
        point: {
          radius: 0,
          hoverRadius: 5,
        },
      },

      plugins: {
        legend: {
          position: 'top',
          align: 'end',
          labels: {
            usePointStyle: true,
            boxWidth: 8,
            padding: 16,
          },
        },
        tooltip: {
          backgroundColor: '#1E1E22',
          titleColor: '#E8E6E0',
          bodyColor: '#888880',
          borderColor: '#2A2A30',
          borderWidth: 1,
          padding: 12,
        },
      },

      scales: {
        x: {
          grid: { color: '#2A2A30' },
          ticks: { color: '#888880' },
        },

        y: {
          position: 'left',
          grid: { color: '#2A2A30' },
          ticks: { color: '#888880' },
          min: 0,
        },

        y1: {
          position: 'right',
          grid: {
            drawOnChartArea: false,
          },
          min: 0,
          ticks: {
            color: '#E8A020',
            callback: (value: number | string) => {
              const num = Number(value)
              if (num >= 3600) return Math.round(num / 3600) + 'h'
              if (num >= 60) return Math.round(num / 60) + 'm'
              return num + 's'
            },
          },
        },
      },
    } satisfies ChartOptions<'line'>
  }
}
