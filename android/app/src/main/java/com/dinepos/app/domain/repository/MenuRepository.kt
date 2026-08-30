package com.dinepos.app.domain.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.MenuItem

interface MenuRepository {
    suspend fun getMenuItems(includeInactive: Boolean = false): Resource<Pair<List<MenuItem>, Int>> // items & nextOrderNumber
    suspend fun toggleItemActive(itemId: Int): Resource<MenuItem>
    suspend fun createItem(name: String, itemType: String, baseUnit: String, active: Boolean, variants: List<Map<String, Any>>): Resource<MenuItem>
}
