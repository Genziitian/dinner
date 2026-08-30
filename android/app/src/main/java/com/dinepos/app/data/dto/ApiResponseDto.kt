package com.dinepos.app.data.dto

import kotlinx.serialization.Serializable

@Serializable
data class ApiResponseDto<T>(
    val success: Boolean,
    val message: String = "",
    val data: T? = null,
    val errors: List<String>? = null
)
