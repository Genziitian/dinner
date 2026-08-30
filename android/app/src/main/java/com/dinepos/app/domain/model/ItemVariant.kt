package com.dinepos.app.domain.model

data class ItemVariant(
    val id: Int,
    val itemId: Int,
    val variantName: String,
    val quantityValue: Double,
    val quantityUnit: String,
    val price: Double,
    val active: Boolean
)
