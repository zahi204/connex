/**
 * Centralised status → color maps.
 * Used by StatusBadge, WorkerAvatar, and page-level logic.
 */
export const useStatusColors = () => {
  // ---- Worker status ----
  function workerStatusBadgeClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'available':          return 'cx-badge-green'
      case 'assigned':           return 'cx-badge-blue'
      case 'future_assignment':  return 'cx-badge-yellow'
      case 'in_training':        return 'cx-badge-orange'
      case 'waiting':            return 'cx-badge-gray'
      case 'inactive':           return 'cx-badge-red'
      case 'frozen':             return 'cx-badge-red'
      default:                   return 'cx-badge-gray'
    }
  }

  function workerStatusLedClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'available':          return 'cx-led-green'
      case 'assigned':           return 'cx-led-blue'
      case 'future_assignment':  return 'cx-led-yellow'
      case 'in_training':        return 'cx-led-blue'
      case 'waiting':            return 'cx-led-gray'
      case 'inactive':           return 'cx-led-red'
      case 'frozen':             return 'cx-led-red'
      default:                   return 'cx-led-gray'
    }
  }

  // ---- Payment status ----
  function paymentStatusBadgeClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'paid':    return 'cx-badge-green'
      case 'overdue': return 'cx-badge-red'
      default:        return 'cx-badge-gray'
    }
  }

  function paymentStatusLedClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'paid':    return 'cx-led-green'
      case 'overdue': return 'cx-led-red'
      default:        return 'cx-led-yellow'
    }
  }

  // ---- Approval status ----
  function approvalStatusBadgeClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'pending':           return 'cx-badge-yellow'
      case 'approved':          return 'cx-badge-green'
      case 'rejected':          return 'cx-badge-red'
      case 'changes_required':  return 'cx-badge-blue'
      default:                  return 'cx-badge-gray'
    }
  }

  // ---- Assignment status ----
  function assignmentStatusBadgeClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'new':       return 'cx-badge-blue'
      case 'active':    return 'cx-badge-green'
      case 'completed': return 'cx-badge-gray'
      case 'cancelled': return 'cx-badge-red'
      default:          return 'cx-badge-gray'
    }
  }

  return {
    workerStatusBadgeClass,
    workerStatusLedClass,
    paymentStatusBadgeClass,
    paymentStatusLedClass,
    approvalStatusBadgeClass,
    assignmentStatusBadgeClass,
  }
}
