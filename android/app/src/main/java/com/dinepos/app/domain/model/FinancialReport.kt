package com.dinepos.app.domain.model

data class FinancialReport(
    val currentPeriod: String,
    val stats: DailyStats,
    val todayStats: DailyStats,
    val yesterdayStats: DailyStats,
    val thisMonthStats: DailyStats,
    val lastMonthStats: DailyStats,
    val yearStats: DailyStats
)
