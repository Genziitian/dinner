package com.dinepos.app.domain.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.*

interface MillRepository {
    suspend fun getServices(): Resource<List<MillServiceDto>>
    suspend fun saveService(service: MillServiceDto): Resource<MillServiceDto>
    suspend fun toggleService(id: Int): Resource<MillServiceDto>
    suspend fun getOrders(status: String? = null, search: String? = null, date: String? = null): Resource<List<MillOrderDto>>
    suspend fun createOrder(request: CreateMillOrderRequestDto): Resource<MillOrderDto>
    suspend fun updateOrderStatus(id: Int, status: String): Resource<MillOrderDto>
    suspend fun updateOrderPayment(id: Int, paymentStatus: String, paymentMethod: String): Resource<MillOrderDto>
    suspend fun deleteOrder(id: Int): Resource<Unit>
    suspend fun getCustomers(search: String? = null): Resource<List<MillCustomerDto>>
    suspend fun getEarnings(
        period: String? = null,
        date: String? = null,
        startDate: String? = null,
        endDate: String? = null
    ): Resource<MillEarningsResponseDto>
    suspend fun getBackup(): Resource<String>
}
