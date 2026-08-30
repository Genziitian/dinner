package com.dinepos.app.presentation.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.User
import com.dinepos.app.domain.repository.AuthRepository
import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharedFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asSharedFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class AuthUiState(
    val isLoading: Boolean = false,
    val username: String = "",
    val password: String = "",
    val errorMessage: String? = null,
    val isLocked: Boolean = false,
    val baseUrl: String = "",
    val showServerConfigDialog: Boolean = false
)

sealed class AuthEvent {
    data class NavigateToHome(val user: User) : AuthEvent()
    data class ShowToast(val message: String) : AuthEvent()
}

class AuthViewModel(
    private val authRepository: AuthRepository = DinePosApp.instance.authRepository
) : ViewModel() {

    private val sessionManager = DinePosApp.instance.sessionManager

    private val _uiState = MutableStateFlow(
        AuthUiState(baseUrl = sessionManager.getBaseUrl())
    )
    val uiState: StateFlow<AuthUiState> = _uiState.asStateFlow()

    private val _events = MutableSharedFlow<AuthEvent>()
    val events: SharedFlow<AuthEvent> = _events.asSharedFlow()

    init {
        checkCachedSession()
    }

    private fun checkCachedSession() {
        val user = authRepository.getCachedUser()
        if (user != null) {
            viewModelScope.launch {
                _events.emit(AuthEvent.NavigateToHome(user))
            }
        }
    }

    fun onUsernameChange(username: String) {
        _uiState.value = _uiState.value.copy(username = username, errorMessage = null)
    }

    fun onPasswordChange(password: String) {
        _uiState.value = _uiState.value.copy(password = password, errorMessage = null)
    }

    fun setShowServerConfig(show: Boolean) {
        _uiState.value = _uiState.value.copy(showServerConfigDialog = show)
    }

    fun saveBaseUrl(newUrl: String) {
        if (newUrl.isNotBlank()) {
            sessionManager.setBaseUrl(newUrl)
            DinePosApp.instance.initRepositories()
            _uiState.value = _uiState.value.copy(
                baseUrl = sessionManager.getBaseUrl(),
                showServerConfigDialog = false,
                errorMessage = null
            )
        }
    }

    fun login() {
        val username = _uiState.value.username.trim()
        val password = _uiState.value.password

        if (username.isBlank() || password.isBlank()) {
            _uiState.value = _uiState.value.copy(errorMessage = "Please enter username and password.")
            return
        }

        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, errorMessage = null)
            when (val result = authRepository.login(username, password)) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(isLoading = false)
                    _events.emit(AuthEvent.NavigateToHome(result.data))
                }
                is Resource.Error -> {
                    val isLock = result.code == 429 || result.message.contains("locked", ignoreCase = true)
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        errorMessage = result.message,
                        isLocked = isLock
                    )
                }
                else -> {
                    _uiState.value = _uiState.value.copy(isLoading = false)
                }
            }
        }
    }
}
