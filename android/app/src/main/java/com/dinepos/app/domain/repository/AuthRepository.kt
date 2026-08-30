package com.dinepos.app.domain.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.User
import kotlinx.coroutines.flow.Flow

interface AuthRepository {
    val isLoggedIn: Flow<Boolean>
    suspend fun login(username: String, password: String): Resource<User>
    suspend fun getCurrentUser(): Resource<User>
    suspend fun logout(): Resource<Unit>
    fun getCachedUser(): User?
}
