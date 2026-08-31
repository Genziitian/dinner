package com.dinepos.app.domain.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.model.FinancialReport
import com.dinepos.app.domain.model.Order

interface ManagerRepository {
    suspend fun getDashboard(): Resource<Pair<DailyStats, List<Order>>>
    suspend fun getReports(period: String = "today", customStart: String? = null, customEnd: String? = null): Resource<FinancialReport>
    suspend fun getAdminOverview(): Resource<com.dinepos.app.data.dto.AdminOverviewResponseDto>
    suspend fun createRestaurant(name: String, phone: String?, address: String?, timezone: String = "Asia/Kolkata"): Resource<com.dinepos.app.data.dto.AdminRestaurantDto>
    suspend fun updateRestaurant(id: Int, name: String, phone: String?, address: String?, timezone: String = "Asia/Kolkata", status: String = "active"): Resource<com.dinepos.app.data.dto.AdminRestaurantDto>
    suspend fun toggleRestaurant(id: Int): Resource<com.dinepos.app.data.dto.AdminRestaurantDto>
    suspend fun createAdminUser(username: String, password: String, role: String, restaurantId: Int?): Resource<com.dinepos.app.data.dto.AdminUserDto>
    suspend fun updateAdminUser(id: Int, username: String, password: String, role: String, restaurantId: Int?, status: String): Resource<com.dinepos.app.data.dto.AdminUserDto>
    suspend fun toggleAdminUser(id: Int): Resource<com.dinepos.app.data.dto.AdminUserDto>
    suspend fun getManagerStaff(): Resource<List<com.dinepos.app.data.dto.AdminUserDto>>
    suspend fun createCashierStaff(username: String, password: String, confirmPassword: String): Resource<com.dinepos.app.data.dto.AdminUserDto>
    suspend fun updateManagerStaff(id: Int, username: String, password: String, confirmPassword: String): Resource<com.dinepos.app.data.dto.AdminUserDto>
    suspend fun toggleManagerStaff(id: Int): Resource<com.dinepos.app.data.dto.AdminUserDto>
    suspend fun getExportData(type: String, date: String? = null, month: String? = null, startDate: String? = null, endDate: String? = null): Resource<com.dinepos.app.data.dto.ExportDataResponseDto>
}
