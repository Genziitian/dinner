package com.dinepos.app.data.api

import com.dinepos.app.data.dto.ApiResponseDto
import com.dinepos.app.data.dto.CashierSummaryResponseDto
import com.dinepos.app.data.dto.CreateItemRequestDto
import com.dinepos.app.data.dto.CreateOrderRequestDto
import com.dinepos.app.data.dto.CreateOrderResponseDto
import com.dinepos.app.data.dto.LoginRequestDto
import com.dinepos.app.data.dto.LoginResponseDto
import com.dinepos.app.data.dto.ManagerDashboardResponseDto
import com.dinepos.app.data.dto.MenuItemDto
import com.dinepos.app.data.dto.MenuResponseDto
import com.dinepos.app.data.dto.OrdersListResponseDto
import com.dinepos.app.data.dto.ReportsResponseDto
import com.dinepos.app.data.dto.SingleOrderResponseDto
import com.dinepos.app.data.dto.UserDto
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Query

interface DinePosApiService {

    @POST("api/v1/auth/login")
    suspend fun login(@Body request: LoginRequestDto): Response<ApiResponseDto<LoginResponseDto>>

    @GET("api/v1/auth/me")
    suspend fun getMe(): Response<ApiResponseDto<UserDto>>

    @GET("api/v1/items")
    suspend fun getItems(@Query("all") all: String? = null): Response<ApiResponseDto<MenuResponseDto>>

    @POST("api/v1/items")
    suspend fun createItem(@Body request: CreateItemRequestDto): Response<ApiResponseDto<MenuItemDto>>

    @POST("api/v1/items/{id}/toggle")
    suspend fun toggleItem(@Path("id") itemId: Int): Response<ApiResponseDto<MenuItemDto>>

    @POST("api/v1/orders")
    suspend fun createOrder(@Body request: CreateOrderRequestDto): Response<ApiResponseDto<CreateOrderResponseDto>>

    @GET("api/v1/orders")
    suspend fun getOrders(
        @Query("filter") filter: String,
        @Query("search") search: String? = null,
        @Query("payment_method") paymentMethod: String? = null,
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null
    ): Response<ApiResponseDto<OrdersListResponseDto>>

    @GET("api/v1/orders/{id}")
    suspend fun getOrderById(@Path("id") orderId: Int): Response<ApiResponseDto<SingleOrderResponseDto>>

    @GET("api/v1/cashier/summary")
    suspend fun getCashierSummary(): Response<ApiResponseDto<CashierSummaryResponseDto>>

    @GET("api/v1/manager/dashboard")
    suspend fun getManagerDashboard(): Response<ApiResponseDto<ManagerDashboardResponseDto>>

    @GET("api/v1/manager/reports")
    suspend fun getReports(
        @Query("period") period: String,
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null
    ): Response<ApiResponseDto<ReportsResponseDto>>

    @GET("api/v1/admin/overview")
    suspend fun getAdminOverview(): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminOverviewResponseDto>>

    @POST("api/v1/admin/restaurants")
    suspend fun createRestaurant(@Body request: com.dinepos.app.data.dto.CreateRestaurantRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminRestaurantDto>>

    @POST("api/v1/admin/restaurants/{id}")
    suspend fun updateRestaurant(@Path("id") id: Int, @Body request: com.dinepos.app.data.dto.CreateRestaurantRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminRestaurantDto>>

    @POST("api/v1/admin/restaurants/{id}/toggle")
    suspend fun toggleRestaurant(@Path("id") id: Int): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminRestaurantDto>>

    @POST("api/v1/admin/users")
    suspend fun createAdminUser(@Body request: com.dinepos.app.data.dto.CreateAdminUserRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminUserDto>>

    @POST("api/v1/admin/users/{id}")
    suspend fun updateAdminUser(@Path("id") id: Int, @Body request: com.dinepos.app.data.dto.CreateAdminUserRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminUserDto>>

    @POST("api/v1/admin/users/{id}/toggle")
    suspend fun toggleAdminUser(@Path("id") id: Int): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminUserDto>>

    @GET("api/v1/manager/staff")
    suspend fun getManagerStaff(): Response<ApiResponseDto<com.dinepos.app.data.dto.StaffListResponseDto>>

    @POST("api/v1/manager/staff")
    suspend fun createManagerStaff(@Body request: com.dinepos.app.data.dto.CreateCashierRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminUserDto>>

    @POST("api/v1/manager/staff/{id}")
    suspend fun updateManagerStaff(@Path("id") id: Int, @Body request: com.dinepos.app.data.dto.CreateCashierRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminUserDto>>

    @POST("api/v1/manager/staff/{id}/toggle")
    suspend fun toggleManagerStaff(@Path("id") id: Int): Response<ApiResponseDto<com.dinepos.app.data.dto.AdminUserDto>>

    @GET("api/v1/manager/export/data")
    suspend fun getExportData(
        @Query("type") type: String,
        @Query("date") date: String? = null,
        @Query("month") month: String? = null,
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null
    ): Response<ApiResponseDto<com.dinepos.app.data.dto.ExportDataResponseDto>>

    @GET("api/v1/receipt/{token}")
    suspend fun getReceiptByToken(@Path("token") token: String): Response<ApiResponseDto<CreateOrderResponseDto>>

    // Mill API
    @GET("api/v1/mill/services")
    suspend fun getMillServices(): Response<ApiResponseDto<List<com.dinepos.app.data.dto.MillServiceDto>>>

    @POST("api/v1/mill/services")
    suspend fun saveMillService(@Body request: com.dinepos.app.data.dto.MillServiceDto): Response<ApiResponseDto<com.dinepos.app.data.dto.MillServiceDto>>

    @POST("api/v1/mill/services/{id}/toggle")
    suspend fun toggleMillService(@Path("id") id: Int): Response<ApiResponseDto<com.dinepos.app.data.dto.MillServiceDto>>

    @GET("api/v1/mill/orders")
    suspend fun getMillOrders(
        @Query("status") status: String? = null,
        @Query("search") search: String? = null,
        @Query("date") date: String? = null
    ): Response<ApiResponseDto<List<com.dinepos.app.data.dto.MillOrderDto>>>

    @POST("api/v1/mill/orders")
    suspend fun createMillOrder(@Body request: com.dinepos.app.data.dto.CreateMillOrderRequestDto): Response<ApiResponseDto<com.dinepos.app.data.dto.MillOrderDto>>

    @POST("api/v1/mill/orders/{id}/status")
    suspend fun updateMillOrderStatus(@Path("id") id: Int, @Body body: Map<String, String>): Response<ApiResponseDto<com.dinepos.app.data.dto.MillOrderDto>>

    @GET("api/v1/mill/customers")
    suspend fun getMillCustomers(@Query("search") search: String? = null): Response<ApiResponseDto<List<com.dinepos.app.data.dto.MillCustomerDto>>>
}
