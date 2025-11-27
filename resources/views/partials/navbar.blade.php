<header class="bg-base-100 shadow-sm z-40 border-b border-base-300 dark:border-gray-700" data-theme="light">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <button onclick="toggleSidebar()" aria-label="Toggle sidebar" class="relative z-50 btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105 pointer-events-auto cursor-pointer">
            <i data-lucide="menu" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent"></i>
          </button>
        </div>
       <div class="flex items-center gap-4">
         <!-- Time Display -->
         <div class="animate-fadeIn">
           <span id="philippineTime" class="font-medium max-md:text-sm"></span>
         </div>
         
          <!-- Notification Dropdown -->
          <div class="dropdown dropdown-end">
            <!-- Button (standard indicator layout) -->
            <button
              id="notification-button"
              tabindex="0"
              class="btn btn-ghost btn-circle cursor-pointer w-12 h-12 md:w-14 md:h-14 transition-transform hover:scale-105"
            >
              <div class="indicator">
                <i data-lucide="bell" class="text-2xl md:text-3xl lg:text-[38px] transition-all duration-300 ease-in-out hover:text-accent"></i>
                @php
                  $unreadCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
                @endphp
                @if($unreadCount > 0)
                  <span class="badge badge-xs badge-error indicator-item">{{ $unreadCount }}</span>
                @endif
              </div>
            </button>
            
            <!-- Dropdown Content - Responsive with proper width -->
            <ul tabindex="0" class="dropdown-content menu mt-3 z-[1] bg-[#001f54] rounded-lg shadow-xl overflow-hidden w-72">
              <!-- Header -->
              <li class="px-3 py-2.5 border-b border-blue-700 flex justify-between items-center sticky top-0 bg-[#001f54] backdrop-blur-sm z-10">
                <div class="flex items-center gap-2">
                  <i data-lucide="bell" class="text-lg text-white"></i>
                  <span class="font-semibold text-white text-sm">Notifications</span>
                </div>
                <button class="text-white hover:text-blue-300 text-xs flex items-center gap-1 cursor-pointer transition-colors" id="clearAllNotificationsBtn">
                  <i data-lucide="trash-2" class="text-sm"></i>
                  <span>Clear All</span>
                </button>
              </li>
              
              <!-- Notification Items Container - Scrollable -->
              <div class="max-h-96 overflow-y-auto" id="notificationsContainer">
                @php
                  $notifications = auth()->user() ? auth()->user()->unreadNotifications()->latest()->take(10)->get() : collect();
                @endphp
                
                @if($notifications->count() > 0)
                  @foreach($notifications as $notification)
                    <li class="px-3 py-2.5 hover:bg-blue-800/50 transition-all notification-item border-b border-blue-800/30" data-notification-id="{{ $notification->id }}">
                      <a class="flex items-start gap-2.5 cursor-pointer" onclick="markAsRead('{{ $notification->id }}')">
                        @php
                          $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                          $type = $notification->type;
                          $title = $data['title'] ?? 'Notification';
                          $message = $data['message'] ?? $data['body'] ?? '';
                          $notificationType = $data['type'] ?? 'info';
                          
                          // Determine icon and color based on type
                          $icon = 'bell';
                          $bgColor = 'bg-blue-600/30';
                          $iconColor = 'text-blue-300';
                          $badge = '';
                          $bgCard = 'bg-blue-700/50';
                          
                          if (isset($data['model_type'])) {
                            // Use model_type from data if available
                            $modelType = strtolower($data['model_type']);
                            
                            if ($modelType === 'document' || str_contains(strtolower($type), 'document') || str_contains(strtolower($type), 'expiration')) {
                            $icon = 'file-text';
                            $bgColor = 'bg-yellow-600/30';
                            $iconColor = 'text-yellow-300';
                            $badge = '<span class="text-xs px-1.5 py-0.5 bg-yellow-600 rounded-full">Expiring</span>';
                            $bgCard = 'bg-yellow-700/50';
                            } elseif ($modelType === 'facility' || str_contains(strtolower($type), 'facility')) {
                              $icon = 'building';
                              $bgColor = 'bg-blue-600/30';
                              $iconColor = 'text-blue-300';
                              $badge = '<span class="text-xs px-1.5 py-0.5 bg-blue-600 rounded-full">New</span>';
                              $bgCard = 'bg-blue-700/50';
                            } elseif ($modelType === 'visitor' || str_contains(strtolower($type), 'visitor') || str_contains(strtolower($type), 'check')) {
                              $icon = 'check-circle';
                              $bgColor = 'bg-green-600/30';
                              $iconColor = 'text-green-300';
                              $badge = '<span class="text-xs px-1.5 py-0.5 bg-green-600 rounded-full">New</span>';
                              $bgCard = 'bg-green-700/50';
                            } elseif ($modelType === 'legal_case' || str_contains(strtolower($type), 'legal')) {
                              $icon = 'scale';
                              $bgColor = 'bg-purple-600/30';
                              $iconColor = 'text-purple-300';
                              $bgCard = 'bg-purple-700/50';
                            } elseif (str_contains(strtolower($type), 'maintenance') || str_contains(strtolower($type), 'alert') || str_contains(strtolower($type), 'security')) {
                              $icon = 'alert-triangle';
                              $bgColor = 'bg-red-600/30';
                              $iconColor = 'text-red-300';
                              $badge = '<span class="text-xs px-1.5 py-0.5 bg-red-600 rounded-full">Urgent</span>';
                              $bgCard = 'bg-red-600';
                            } elseif (str_contains(strtolower($type), 'reservation')) {
                              $icon = 'calendar-check';
                              $bgColor = 'bg-blue-600/30';
                              $iconColor = 'text-blue-300';
                              $badge = '<span class="text-xs px-1.5 py-0.5 bg-blue-600 rounded-full">New</span>';
                              $bgCard = 'bg-blue-700/50';
                            } elseif (str_contains(strtolower($type), 'message')) {
                              $icon = 'message-circle';
                              $bgColor = 'bg-purple-600/30';
                              $iconColor = 'text-purple-300';
                              $bgCard = 'bg-purple-700/50';
                            } elseif (str_contains(strtolower($type), 'payment')) {
                              $icon = 'credit-card';
                              $bgColor = 'bg-green-600/30';
                              $iconColor = 'text-green-300';
                              $bgCard = 'bg-green-700/50';
                            }
                          } elseif (str_contains(strtolower($type), 'document') || str_contains(strtolower($type), 'expiration')) {
                            $icon = 'file-text';
                            $bgColor = 'bg-yellow-600/30';
                            $iconColor = 'text-yellow-300';
                            $badge = '<span class="text-xs px-1.5 py-0.5 bg-yellow-600 rounded-full">Expiring</span>';
                            $bgCard = 'bg-yellow-700/50';
                          } elseif (str_contains(strtolower($type), 'maintenance') || str_contains(strtolower($type), 'alert')) {
                            $icon = 'alert-triangle';
                            $bgColor = 'bg-red-600/30';
                            $iconColor = 'text-red-300';
                            $badge = '<span class="text-xs px-1.5 py-0.5 bg-red-600 rounded-full">Urgent</span>';
                            $bgCard = 'bg-red-600';
                          } elseif (str_contains(strtolower($type), 'visitor') || str_contains(strtolower($type), 'check')) {
                            $icon = 'check-circle';
                            $bgColor = 'bg-green-600/30';
                            $iconColor = 'text-green-300';
                            $badge = '<span class="text-xs px-1.5 py-0.5 bg-green-600 rounded-full">New</span>';
                            $bgCard = 'bg-green-700/50';
                          } elseif (str_contains(strtolower($type), 'reservation')) {
                            $icon = 'calendar-check';
                            $bgColor = 'bg-blue-600/30';
                            $iconColor = 'text-blue-300';
                            $badge = '<span class="text-xs px-1.5 py-0.5 bg-blue-600 rounded-full">New</span>';
                            $bgCard = 'bg-blue-700/50';
                          } elseif (str_contains(strtolower($type), 'security')) {
                            $icon = 'alert-octagon';
                            $bgColor = 'bg-red-600/30';
                            $iconColor = 'text-red-300';
                            $badge = '<span class="text-xs px-1.5 py-0.5 bg-red-600 rounded-full">Alert</span>';
                            $bgCard = 'bg-red-600';
                          } elseif (str_contains(strtolower($type), 'message')) {
                            $icon = 'message-circle';
                            $bgColor = 'bg-purple-600/30';
                            $iconColor = 'text-purple-300';
                            $bgCard = 'bg-purple-700/50';
                          } elseif (str_contains(strtolower($type), 'payment')) {
                            $icon = 'credit-card';
                            $bgColor = 'bg-green-600/30';
                            $iconColor = 'text-green-300';
                            $bgCard = 'bg-green-700/50';
                          }
                        @endphp
                        <div class="p-2 rounded-full {{ $bgColor }} flex-shrink-0">
                          <i data-lucide="{{ $icon }}" class="text-base text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="font-medium text-white text-xs flex items-center gap-1.5 mb-0.5">
                            {{ $title }}
                            {!! $badge !!}
                          </p>
                          <p class="text-xs text-blue-200 mt-0.5 line-clamp-2">{{ $message }}</p>
                          <p class="text-[10px] text-blue-300 mt-1 flex items-center gap-1">
                            <i data-lucide="clock" class="w-2.5 h-2.5"></i>
                            {{ $notification->created_at->diffForHumans() }}
                          </p>
                        </div>
                      </a>
                    </li>
                  @endforeach
                @else
                  <!-- Empty State -->
                  <li class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center gap-2">
                      <div class="p-3 rounded-full bg-blue-600/20">
                        <i data-lucide="bell-off" class="text-3xl text-blue-300"></i>
                      </div>
                      <p class="text-white font-semibold text-sm">No notifications</p>
                      <p class="text-xs text-blue-300">You're all caught up!</p>
                    </div>
                  </li>
                @endif
              </div>
              
              <!-- Footer -->
              <li class="px-3 py-2 border-t border-blue-700 sticky bottom-0 bg-[#001f54] backdrop-blur-sm">
                <a href="#" class="text-center text-white hover:text-blue-300 text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-colors">
                  <i data-lucide="list" class="text-sm"></i>
                  <span>View All Notifications</span>
                </a>
              </li>
            </ul>
          </div>

          <!-- User Dropdown -->
          <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle avatar cursor-pointer">
              <div class="w-8 rounded-full">
                <img src="{{asset('images/avatars/empl.jpg')}}" alt="User Avatar" />
              </div>
            </label>
            <ul tabindex="0" class="dropdown-content menu mt-1 z-[100] w-52 bg-[#001f54] rounded-box shadow-xl">
              <!-- User Profile Section -->
              <li class="p-3 border-b ">
                <div class="bg-blue-700/50 rounded-md shadow-md flex items-center gap-3">
                  <div class="avatar">
                    <div class="w-10 rounded-full">
                      <img src="{{asset('images/avatars/empl.jpg')}}" alt="User Avatar" class="dark:brightness-90" />
                    </div>
                  </div>
                  <div>
                    @php
                      // Resolve current employee_id with fallbacks
                      $empId = session('emp_id');
                      if (empty($empId)) {
                        $empId = auth()->user()->employee_id ?? null;
                      }
                      if (empty($empId)) {
                        $email = auth()->user()->email ?? '';
                        if (strpos($email, '@') !== false) {
                          $empId = substr($email, 0, strpos($email, '@'));
                        }
                      }
                      $displayName = auth()->user()->name ?? null;
                      $displayRole = auth()->user()->role ?? null;
                      // Prefer department_accounts data if we have an employee_id
                      if (!empty($empId)) {
                        try {
                          $deptUser = \Illuminate\Support\Facades\DB::table('department_accounts')->where('employee_id', $empId)->first();
                          if ($deptUser) {
                            $displayName = $deptUser->employee_name ?: $displayName;
                            $displayRole = $deptUser->role ?: $displayRole;
                          }
                        } catch (\Throwable $e) { /* silent fallback */ }
                      }
                    @endphp
                    <p class="font-medium text-white">{{ $displayName ?? 'User' }}</p>
                    <p class="text-xs text-white">{{ ucfirst($displayRole ?? 'User') }}</p>
                  </div>
                </div>
              </li>
              
              <!-- Menu Items -->
              <li>
                <a class="flex items-center gap-2 px-4 py-2 text-white hover:bg-blue-700/50 transition-colors cursor-pointer">
                  <i data-lucide="user" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent"></i>
                  <span>Profile</span>
                </a>
              </li>
              <li>
                <a class="flex items-center gap-2 px-4 py-2 text-white hover:bg-blue-700/50 transition-colors cursor-pointer">
                  <i data-lucide="settings" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent"></i>
                  <span>Settings</span>
                </a>
              </li>
              <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 px-4 py-2 text-white hover:bg-blue-700/50 transition-colors cursor-pointer">
                  <i data-lucide="log-out" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent"></i>
                  <span>Sign out</span>
                </a>
              </li>
            </ul>
            <!-- Hidden logout form -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
              @csrf
            </form>
          </div>
        </div>
      </div>
    </div>
  </header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clearAllBtn = document.getElementById('clearAllNotificationsBtn');
    const notificationItems = document.querySelectorAll('.notification-item');
    
    // Clear All Notifications functionality
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Show confirmation
            if (confirm('Are you sure you want to clear all notifications?')) {
                // Call API to mark all notifications as read
                fetch('/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Remove all notification items except View All button
                    const itemsToRemove = document.querySelectorAll('.notification-item');
                    itemsToRemove.forEach(item => {
                        item.style.transition = 'opacity 0.3s ease-out';
                        item.style.opacity = '0';
                        setTimeout(() => {
                            item.remove();
                        }, 300);
                    });
                    
                    // Update badge count to 0
                    const badge = document.querySelector('.badge-error.indicator-item');
                    if (badge) {
                        badge.style.display = 'none';
                    }
                    
                    // Show success message
                    showNotification('All notifications cleared successfully!', 'success');
                })
                .catch(error => {
                    console.error('Error clearing notifications:', error);
                    showNotification('Error clearing notifications', 'error');
                });
            }
        });
    }
    
    // View All Notifications functionality
    const viewAllLinks = document.querySelectorAll('.dropdown-content li a');
    if (viewAllLinks.length > 0) {
        // Find the link with "View All Notifications" text (the last one)
        const viewAllLink = Array.from(viewAllLinks).find(link => 
            link.textContent.trim().includes('View All')
        );
        
        if (viewAllLink) {
            viewAllLink.addEventListener('click', function(e) {
                e.preventDefault();
                showNotification('Redirecting to all notifications page...', 'info');
                
                // You can uncomment this to redirect to a notifications page if you have one
                // window.location.href = '/notifications';
            });
        }
    }
    
    // Handle individual notification clicks
    notificationItems.forEach(item => {
        const link = item.querySelector('a');
        if (link) {
            link.addEventListener('click', function(e) {
                // Mark as read by adding visual feedback
                if (!this.classList.contains('opacity-70')) {
                    this.classList.add('opacity-70');
                }
                
                // You can add more functionality here for specific notification types
                const notificationTitle = this.querySelector('.font-medium')?.textContent;
                
                if (notificationTitle) {
                    // Example: Route to specific pages based on notification type
                    if (notificationTitle.includes('Reservation')) {
                        // Handle reservation notification
                        console.log('Reservation notification clicked');
                    } else if (notificationTitle.includes('Maintenance')) {
                        // Handle maintenance notification
                        console.log('Maintenance notification clicked');
                    } else if (notificationTitle.includes('Check-in')) {
                        // Handle check-in notification
                        console.log('Check-in notification clicked');
                    }
                }
            });
        }
    });
    
    // Function to show notification messages
    // Use global showNotification with progress bar if available
    if (typeof window.showNotification === 'undefined' || window.showNotification.toString().indexOf('progressBar') === -1) {
      window.showNotification = function(message, type = 'info', duration = 3000) {
        if (!document.getElementById('notification-progress-style')) {
          const style = document.createElement('style');
          style.id = 'notification-progress-style';
          style.textContent = `
            @keyframes progressBar {
              from { width: 100%; }
              to { width: 0%; }
            }
            @keyframes slideInRight {
              from { transform: translateX(100%); opacity: 0; }
              to { transform: translateX(0); opacity: 1; }
            }
          `;
          document.head.appendChild(style);
        }

        const notification = document.createElement('div');
        const alertType = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
        notification.className = `alert alert-${alertType} fixed bottom-4 right-4 z-[9999] max-w-sm shadow-lg relative overflow-hidden`;
        notification.style.cssText = 'position: fixed; bottom: 1rem; right: 1rem; z-index: 9999; max-width: 24rem; animation: slideInRight 0.3s ease-out;';
        
        const iconMap = { 'success': 'check-circle', 'error': 'alert-circle', 'warning': 'alert-triangle', 'info': 'info' };
        const icon = iconMap[type] || 'info';
        
        notification.innerHTML = `
          <div class="flex items-center gap-2 px-4 py-3">
            <i data-lucide="${icon}" class="w-5 h-5"></i>
            <span>${message}</span>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
            <div class="notification-progress h-full bg-white/50" style="width: 100%; animation: progressBar ${duration}ms linear forwards;"></div>
          </div>
        `;
        
        document.body.appendChild(notification);
        notification.offsetHeight;
        
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
        
        setTimeout(() => {
          notification.style.opacity = '0';
          notification.style.transition = 'opacity 0.3s ease-out';
          setTimeout(() => {
            if (notification.parentNode) notification.remove();
          }, 300);
        }, duration);
      };
    }
    
    // Auto-refresh notification count every 2 seconds
    let lastNotificationCount = {{ auth()->user() ? auth()->user()->unreadNotifications()->count() : 0 }};
    
    setInterval(() => {
        fetch('/api/notifications/count', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.count !== lastNotificationCount) {
                // If new notifications arrived, reload the page to show them
                if (data.count > lastNotificationCount) {
                    location.reload();
                } else {
                    lastNotificationCount = data.count;
                }
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
    }, 2000); // Check every 2 seconds
});

// Mark notification as read
function markAsRead(notificationId) {
    fetch('/notifications/' + notificationId + '/mark-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update badge count
        const badge = document.querySelector('.badge-error.indicator-item');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            const newCount = Math.max(0, currentCount - 1);
            if (newCount > 0) {
                badge.textContent = newCount;
            } else {
                badge.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}
</script>