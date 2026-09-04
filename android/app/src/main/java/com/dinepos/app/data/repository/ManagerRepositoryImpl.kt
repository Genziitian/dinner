package com.dinepos.app.data.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.api.DinePosApiService
import com.dinepos.app.data.dto.DailyStatsDto
import com.dinepos.app.data.dto.OrderDto
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.model.FinancialReport
import com.dinepos.app.domain.model.Order
import com.dinepos.app.domain.model.OrderItem
import com.dinepos.app.domain.repository.ManagerRepository

class ManagerRepositoryImpl(private val apiService: DinePosApiService) : ManagerRepository {

    override suspend fun getDashboard(): Resource<Pair<DailyStats, List<Order>>> {
        return try {
            val response = apiService.getManagerDashboard()
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                val statsDto = data?.stats ?: DailyStatsDto()
                val stats = DailyStats(
                    totalOrders = statsDto.totalOrders,
                    totalSales = statsDto.totalSales,
                    cashSales = statsDto.cashSales,
                    onlineSales = statsDto.onlineSales,
                    avgOrderValue = statsDto.avgOrderValue,
                    unpaidAmount = statsDto.unpaidAmount,
                    totalWeightKg = statsDto.totalWeightKg
                )
                val sessionMgr = com.dinepos.app.DinePosApp.instance.sessionManager
                val orders = data?.recentOrders?.map { mapOrderDto(it) }?.filter {
                    !sessionMgr.isOrderDeleted(it.id) &&
                    !it.status.equals("cancelled", ignoreCase = true)
                } ?: emptyList()
                val shopType = data?.restaurant?.shopType
                if (!shopType.isNullOrBlank()) {
                    sessionMgr.saveShopType(shopType)
                }
                Resource.Success(Pair(stats, orders))
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load dashboard.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getReports(period: String, customStart: String?, customEnd: String?): Resource<FinancialReport> {
        return try {
            val response = apiService.getReports(period, customStart, customEnd)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                val stats = mapStatsDto(data?.stats ?: DailyStatsDto())
                val comp = data?.comparison

                val report = FinancialReport(
                    currentPeriod = data?.currentPeriod ?: period,
                    stats = stats,
                    todayStats = mapStatsDto(comp?.today ?: DailyStatsDto()),
                    yesterdayStats = mapStatsDto(comp?.yesterday ?: DailyStatsDto()),
                    thisMonthStats = mapStatsDto(comp?.thisMonth ?: DailyStatsDto()),
                    lastMonthStats = mapStatsDto(comp?.lastMonth ?: DailyStatsDto()),
                    yearStats = mapStatsDto(comp?.year ?: DailyStatsDto())
                )
                Resource.Success(report)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load reports.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getAdminOverview(): Resource<com.dinepos.app.data.dto.AdminOverviewResponseDto> {
        return try {
            val response = apiService.getAdminOverview()
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data ?: com.dinepos.app.data.dto.AdminOverviewResponseDto()
                Resource.Success(data)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load super admin overview.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun createRestaurant(
        name: String,
        phone: String?,
        address: String?,
        timezone: String
    ): Resource<com.dinepos.app.data.dto.AdminRestaurantDto> {
        return try {
            val req = com.dinepos.app.data.dto.CreateRestaurantRequestDto(name, phone, address, timezone, "active")
            val response = apiService.createRestaurant(req)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to create restaurant.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun updateRestaurant(
        id: Int,
        name: String,
        phone: String?,
        address: String?,
        timezone: String,
        status: String
    ): Resource<com.dinepos.app.data.dto.AdminRestaurantDto> {
        return try {
            val req = com.dinepos.app.data.dto.CreateRestaurantRequestDto(name, phone, address, timezone, status)
            val response = apiService.updateRestaurant(id, req)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to update restaurant.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun toggleRestaurant(id: Int): Resource<com.dinepos.app.data.dto.AdminRestaurantDto> {
        return try {
            val response = apiService.toggleRestaurant(id)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to toggle restaurant status.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun createAdminUser(
        username: String,
        password: String,
        role: String,
        restaurantId: Int?
    ): Resource<com.dinepos.app.data.dto.AdminUserDto> {
        return try {
            val req = com.dinepos.app.data.dto.CreateAdminUserRequestDto(username, password, role, restaurantId, "active")
            val response = apiService.createAdminUser(req)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to create user.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun updateAdminUser(
        id: Int,
        username: String,
        password: String,
        role: String,
        restaurantId: Int?,
        status: String
    ): Resource<com.dinepos.app.data.dto.AdminUserDto> {
        return try {
            val req = com.dinepos.app.data.dto.CreateAdminUserRequestDto(username, password, role, restaurantId, status)
            val response = apiService.updateAdminUser(id, req)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to update user.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun toggleAdminUser(id: Int): Resource<com.dinepos.app.data.dto.AdminUserDto> {
        return try {
            val response = apiService.toggleAdminUser(id)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to toggle user status.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getManagerStaff(): Resource<List<com.dinepos.app.data.dto.AdminUserDto>> {
        return try {
            val response = apiService.getManagerStaff()
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!.staff)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load staff list.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun createCashierStaff(
        username: String,
        password: String,
        confirmPassword: String
    ): Resource<com.dinepos.app.data.dto.AdminUserDto> {
        return try {
            val req = com.dinepos.app.data.dto.CreateCashierRequestDto(username, password, confirmPassword)
            val response = apiService.createManagerStaff(req)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to create cashier staff.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun updateManagerStaff(
        id: Int,
        username: String,
        password: String,
        confirmPassword: String
    ): Resource<com.dinepos.app.data.dto.AdminUserDto> {
        return try {
            val req = com.dinepos.app.data.dto.CreateCashierRequestDto(username, password, confirmPassword)
            val response = apiService.updateManagerStaff(id, req)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to update cashier staff.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun toggleManagerStaff(id: Int): Resource<com.dinepos.app.data.dto.AdminUserDto> {
        return try {
            val response = apiService.toggleManagerStaff(id)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to toggle staff status.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getExportData(
        type: String,
        date: String?,
        month: String?,
        startDate: String?,
        endDate: String?
    ): Resource<com.dinepos.app.data.dto.ExportDataResponseDto> {
        return try {
            val response = apiService.getExportData(type, date, month, startDate, endDate)
            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Resource.Success(response.body()!!.data!!)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to fetch export data.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    private fun mapStatsDto(dto: DailyStatsDto): DailyStats {
        return DailyStats(
            totalOrders = dto.totalOrders,
            totalSales = dto.totalSales,
            cashSales = dto.cashSales,
            onlineSales = dto.onlineSales,
            avgOrderValue = dto.avgOrderValue,
            unpaidAmount = dto.unpaidAmount,
            totalWeightKg = dto.totalWeightKg
        )
    }

    private fun mapOrderDto(dto: OrderDto): Order {
        return Order(
            id = dto.id,
            restaurantId = dto.restaurantId,
            orderNumber = dto.orderNumber,
            orderDate = dto.orderDate,
            orderTime = dto.orderTime,
            customerName = dto.customerName,
            customerPhone = dto.customerPhone,
            subtotal = if (dto.subtotal > 0.0) dto.subtotal else (dto.totalAmount ?: 0.0),
            total = if (dto.total > 0.0) dto.total else (dto.totalAmount ?: 0.0),
            paymentMethod = dto.paymentMethod,
            status = dto.status,
            receiptTokenHash = dto.receiptTokenHash,
            createdByUsername = dto.createdByUsername,
            items = dto.items.map { itemDto ->
                OrderItem(
                    id = itemDto.id,
                    orderId = itemDto.orderId,
                    itemId = itemDto.itemId,
                    itemName = if (itemDto.itemName.isNotBlank()) itemDto.itemName else (itemDto.itemNameDirect ?: ""),
                    variantName = if (itemDto.variantName.isNotBlank()) itemDto.variantName else (itemDto.variantNameDirect ?: ""),
                    quantity = itemDto.quantity,
                    unit = itemDto.unit,
                    unitPrice = itemDto.unitPrice,
                    totalPrice = itemDto.totalPrice
                )
            }
        )
    }
}
