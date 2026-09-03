package com.dinepos.app.domain.model

data class DailyStats(
    val totalOrders: Int,
    val totalSales: Double,
    val cashSales: Double,
    val onlineSales: Double,
    val avgOrderValue: Double = 0.0,
    val unpaidAmount: Double = 0.0,
    val totalWeightKg: Double = 0.0
)
