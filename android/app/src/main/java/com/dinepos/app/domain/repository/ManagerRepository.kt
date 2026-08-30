package com.dinepos.app.domain.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.model.FinancialReport
import com.dinepos.app.domain.model.Order

interface ManagerRepository {
    suspend fun getDashboard(): Resource<Pair<DailyStats, List<Order>>>
    suspend fun getReports(period: String = "today", customStart: String? = null, customEnd: String? = null): Resource<FinancialReport>
}
