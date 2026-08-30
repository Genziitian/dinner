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
                    avgOrderValue = statsDto.avgOrderValue
                )
                val orders = data?.recentOrders?.map { mapOrderDto(it) } ?: emptyList()
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

    private fun mapStatsDto(dto: DailyStatsDto): DailyStats {
        return DailyStats(
            totalOrders = dto.totalOrders,
            totalSales = dto.totalSales,
            cashSales = dto.cashSales,
            onlineSales = dto.onlineSales,
            avgOrderValue = dto.avgOrderValue
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
            subtotal = dto.subtotal,
            total = dto.total,
            paymentMethod = dto.paymentMethod,
            status = dto.status,
            receiptTokenHash = dto.receiptTokenHash,
            createdByUsername = dto.createdByUsername,
            items = dto.items.map { itemDto ->
                OrderItem(
                    id = itemDto.id,
                    orderId = itemDto.orderId,
                    itemId = itemDto.itemId,
                    itemName = itemDto.itemName,
                    variantName = itemDto.variantName,
                    quantity = itemDto.quantity,
                    unit = itemDto.unit,
                    unitPrice = itemDto.unitPrice,
                    totalPrice = itemDto.totalPrice
                )
            }
        )
    }
}
