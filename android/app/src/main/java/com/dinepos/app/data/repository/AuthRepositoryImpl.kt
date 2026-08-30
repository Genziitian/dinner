package com.dinepos.app.data.repository

import com.dinepos.app.core.security.SessionManager
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.api.DinePosApiService
import com.dinepos.app.data.dto.LoginRequestDto
import com.dinepos.app.domain.model.User
import com.dinepos.app.domain.repository.AuthRepository
import kotlinx.coroutines.flow.Flow

class AuthRepositoryImpl(
    private val apiService: DinePosApiService,
    private val sessionManager: SessionManager
) : AuthRepository {

    override val isLoggedIn: Flow<Boolean> = sessionManager.isLoggedIn

    override suspend fun login(username: String, password: String): Resource<User> {
        return try {
            val response = apiService.login(LoginRequestDto(username = username, password = password))
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                if (data != null) {
                    sessionManager.saveSession(
                        token = data.token,
                        userId = data.user.id,
                        username = data.user.username,
                        role = data.user.role,
                        restaurantId = data.user.restaurantId,
                        restaurantName = data.restaurant?.name
                    )
                    val user = User(
                        id = data.user.id,
                        username = data.user.username,
                        role = data.user.role,
                        restaurantId = data.user.restaurantId,
                        restaurantName = data.restaurant?.name
                    )
                    Resource.Success(user, response.body()?.message)
                } else {
                    Resource.Error("Invalid server response.")
                }
            } else {
                val errorMsg = response.body()?.message ?: response.errorBody()?.string() ?: "Login failed."
                Resource.Error(errorMsg, response.code())
            }
        } catch (e: Exception) {
            Resource.Error("Connection error: ${e.localizedMessage ?: "Unable to reach server."}")
        }
    }

    override suspend fun getCurrentUser(): Resource<User> {
        val cached = getCachedUser()
        return if (cached != null) {
            Resource.Success(cached)
        } else {
            Resource.Error("No active user session.")
        }
    }

    override suspend fun logout(): Resource<Unit> {
        sessionManager.clearSession()
        return Resource.Success(Unit)
    }

    override fun getCachedUser(): User? {
        val token = sessionManager.getAuthToken()
        if (token.isNullOrBlank()) return null
        return User(
            id = sessionManager.getUserId(),
            username = sessionManager.getUsername(),
            role = sessionManager.getUserRole(),
            restaurantId = sessionManager.getRestaurantId().takeIf { it > 0 },
            restaurantName = sessionManager.getRestaurantName()
        )
    }
}
