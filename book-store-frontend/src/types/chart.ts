import type { ChartData, ChartOptions, ChartTypeRegistry } from 'chart.js'

export interface AppChartConfig<TType extends keyof ChartTypeRegistry = 'line'> {
  type: TType
  data: ChartData<TType>
  options: ChartOptions<TType>
}
