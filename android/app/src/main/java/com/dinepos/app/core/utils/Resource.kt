package com.dinepos.app.core.utils

sealed class Resource<out T> {
    data class Success<out T>(val data: T, val message: String? = null) : Resource<T>()
    data class Error(val message: String, val code: Int? = null) : Resource<Nothing>()
    object Loading : Resource<Nothing>()
    object Idle : Resource<Nothing>()
}
