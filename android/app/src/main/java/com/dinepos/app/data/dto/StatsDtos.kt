package com.dinepos.app.data.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class CashierSummaryResponseDto(
    val stats: DailyStatsDto,
    @SerialName("today_date") val todayDate: String = "",
    val restaurant: RestaurantDto? = null
)

@Serializable
data class ManagerDashboardResponseDto(
    val stats: DailyStatsDto,
    @SerialName("recent_orders") val recentOrders: List<OrderDto> = emptyList(),
    val restaurant: RestaurantDto? = null
)

@Serializable
data class ReportsResponseDto(
    @SerialName("current_period") val currentPeriod: String = "today",
    val stats: DailyStatsDto,
    val comparison: ReportsComparisonDto? = null
)

@Serializable
data class ReportsComparisonDto(
    val today: DailyStatsDto? = null,
    val yesterday: DailyStatsDto? = null,
    @SerialName("this_month") val thisMonth: DailyStatsDto? = null,
    @SerialName("last_month") val lastMonth: DailyStatsDto? = null,
    @SerialName("six_months") val sixMonths: DailyStatsDto? = null,
    val year: DailyStatsDto? = null
)

@Serializable
data class DailyStatsDto(
    @SerialName("total_orders") val totalOrders: Int = 0,
    @SerialName("total_sales") val totalSales: Double = 0.0,
    @SerialName("cash_sales") val cashSales: Double = 0.0,
    @SerialName("online_sales") val onlineSales: Double = 0.0,
    @SerialName("avg_order_value") val avgOrderValue: Double = 0.0
)
