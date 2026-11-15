/**
 * Cập nhật trực tiếp: tải lại dữ liệu theo chu kỳ mà không cần F5.
 * Tự tạm dừng khi tab bị ẩn và chạy lại ngay khi người dùng quay lại tab.
 */
export function liveRefresh(task, options = {}) {
  const interval = Math.max(5, Number(options.interval) || 20) * 1000;
  let timer = null;
  let running = false;
  let stopped = false;

  async function run() {
    if (running || stopped || document.hidden) return;
    running = true;
    try {
      await task();
    } catch {
      // Giữ nguyên dữ liệu đang hiển thị khi mất mạng hoặc API tạm lỗi.
    } finally {
      running = false;
    }
  }

  function clearTimer() {
    if (timer !== null) {
      window.clearInterval(timer);
      timer = null;
    }
  }

  function schedule() {
    if (stopped) return;
    clearTimer();
    timer = window.setInterval(run, interval);
  }

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      clearTimer();
    } else {
      schedule();
      run();
    }
  });
  window.addEventListener('online', () => run());

  if (options.immediate !== false) run();
  schedule();

  return {
    refresh: run,
    stop() {
      stopped = true;
      clearTimer();
    },
  };
}

export function sameJson(a, b) {
  try {
    return JSON.stringify(a) === JSON.stringify(b);
  } catch {
    return false;
  }
}
