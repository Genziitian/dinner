package com.dinepos.app

import android.app.Application
import com.dinepos.app.core.network.NetworkModule
import com.dinepos.app.core.security.SessionManager
import com.dinepos.app.data.repository.AuthRepositoryImpl
import com.dinepos.app.data.repository.ManagerRepositoryImpl
import com.dinepos.app.data.repository.MenuRepositoryImpl
import com.dinepos.app.data.repository.OrderRepositoryImpl
import com.dinepos.app.domain.repository.AuthRepository
import com.dinepos.app.domain.repository.ManagerRepository
import com.dinepos.app.domain.repository.MenuRepository
import com.dinepos.app.domain.repository.OrderRepository

class DinePosApp : Application() {

    lateinit var sessionManager: SessionManager private set
    lateinit var networkModule: NetworkModule private set

    lateinit var authRepository: AuthRepository private set
    lateinit var menuRepository: MenuRepository private set
    lateinit var orderRepository: OrderRepository private set
    lateinit var managerRepository: ManagerRepository private set

    override fun onCreate() {
        super.onCreate()
        instance = this

        sessionManager = SessionManager(this)
        networkModule = NetworkModule(sessionManager)

        initRepositories()
    }

    fun initRepositories() {
        val apiService = networkModule.provideApiService()
        authRepository = AuthRepositoryImpl(apiService, sessionManager)
        menuRepository = MenuRepositoryImpl(apiService)
        orderRepository = OrderRepositoryImpl(apiService)
        managerRepository = ManagerRepositoryImpl(apiService)
    }

    companion object {
        lateinit var instance: DinePosApp private set
    }
}
