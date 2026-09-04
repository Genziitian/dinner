package com.dinepos.app.data.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.api.DinePosApiService
import com.dinepos.app.data.dto.*
import com.dinepos.app.domain.repository.MillRepository

class MillRepositoryImpl(private val apiService: DinePosApiService) : MillRepository {

    override suspend fun getServices(): Resource<List<MillServiceDto>> {
        return try {
            val response = apiService.getMillServices()
            if (response.isSuccessful && response.body()?.success == true) {
                Resource.Success(response.body()?.data ?: emptyList())
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load mill services.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun saveService(service: MillServiceDto): Resource<MillServiceDto> {
        return try {
            val response = apiService.saveMillService(service)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) Resource.Success(data, response.body()?.message)
                else Resource.Error("Invalid response")
            } else {
                Resource.Error(response.body()?.message ?: "Failed to save service.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun toggleService(id: Int): Resource<MillServiceDto> {
        return try {
            val response = apiService.toggleMillService(id)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) Resource.Success(data)
                else Resource.Error("Invalid response")
            } else {
                Resource.Error(response.body()?.message ?: "Failed to toggle service.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getOrders(status: String?, search: String?, date: String?): Resource<List<MillOrderDto>> {
        return try {
            val response = apiService.getMillOrders(status = status, search = search, date = date)
            if (response.isSuccessful && response.body()?.success == true) {
                Resource.Success(response.body()?.data ?: emptyList())
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load orders.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun createOrder(request: CreateMillOrderRequestDto): Resource<MillOrderDto> {
        return try {
            val response = apiService.createMillOrder(request)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) Resource.Success(data, response.body()?.message)
                else Resource.Error("Invalid response")
            } else {
                Resource.Error(response.body()?.message ?: "Failed to create order.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun updateOrderStatus(id: Int, status: String): Resource<MillOrderDto> {
        return try {
            val response = apiService.updateMillOrderStatus(id, mapOf("status" to status))
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) Resource.Success(data, response.body()?.message)
                else Resource.Error("Invalid response")
            } else {
                Resource.Error(response.body()?.message ?: "Failed to update order status.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun updateOrderPayment(id: Int, paymentStatus: String, paymentMethod: String): Resource<MillOrderDto> {
        return try {
            val response = apiService.updateMillOrderStatus(id, mapOf("payment_status" to paymentStatus, "payment_method" to paymentMethod))
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) Resource.Success(data, response.body()?.message)
                else Resource.Error("Invalid response")
            } else {
                Resource.Error(response.body()?.message ?: "Failed to update payment status.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun deleteOrder(id: Int): Resource<Unit> {
        return try {
            val response = apiService.deleteMillOrder(id)
            if (response.isSuccessful && response.body()?.success == true) {
                Resource.Success(Unit)
            } else {
                apiService.updateMillOrderStatus(id, mapOf("status" to "cancelled"))
                Resource.Success(Unit)
            }
        } catch (e: Exception) {
            try {
                apiService.updateMillOrderStatus(id, mapOf("status" to "cancelled"))
                Resource.Success(Unit)
            } catch (fallbackEx: Exception) {
                Resource.Error("Error deleting order: ${e.localizedMessage}")
            }
        }
    }

    override suspend fun getCustomers(search: String?): Resource<List<MillCustomerDto>> {
        return try {
            val response = apiService.getMillCustomers(search = search)
            if (response.isSuccessful && response.body()?.success == true) {
                Resource.Success(response.body()?.data ?: emptyList())
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load customers.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getEarnings(
        period: String?,
        date: String?,
        startDate: String?,
        endDate: String?
    ): Resource<MillEarningsResponseDto> {
        return try {
            val response = apiService.getMillEarnings(
                period = period,
                date = date,
                startDate = startDate,
                endDate = endDate
            )
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) Resource.Success(data)
                else Resource.Error("Empty earnings data received.")
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load earnings.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getBackup(): Resource<String> {
        return try {
            val response = apiService.getMillBackup()
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) {
                    Resource.Success(data.toString())
                } else {
                    Resource.Error("Empty backup data received.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Failed to fetch mill backup.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }
}
