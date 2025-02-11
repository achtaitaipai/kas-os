let id = 0;

function makeId() {
  return id++;
}

export class WindowData {
  public path: string = $state("");
  public left: number = $state(100);
  public top: number = $state(100);
  public width: number = $state(500);
  public height: number = $state(500);
  public fold: boolean = $state(false);
  public fullscreen: boolean = $state(false);
  public active = $state(false);
  public timestamp: number;
  public id: number;

  constructor(path: string, top: number, left: number) {
    this.path = path;
    this.id = makeId();
    this.timestamp = Date.now();
    this.top = top;
    this.left = left;
  }
}

let windows: WindowData[] = $state([]);

export function getWindows() {
  return windows;
}

export function getSortedWindows() {
  return windows.toSorted((a, b) => a.timestamp - b.timestamp);
}

export function openWindow(path: string) {
  const target = windows.find((el) => el.path === path);
  if (target) {
    activeWindow(target.id);
    target.fold = false;
    return;
  }
  const item = new WindowData(
    path,
    20 * (windows.length % 10) + 50,
    20 * (windows.length % 10) + 50,
  );
  windows = [...windows, item];
  activeWindow(item.id);
}

export function closeWindow(id: number) {
  windows = windows.filter((el) => el.id !== id);
}

export function activeWindow(id: number) {
  const window = windows.find((el) => el.id === id);
  if (!window || window.active) return;
  window.active = true;
  windows.forEach((el) => {
    if (el.active && el.id !== id) el.active = false;
  });
  windows.sort((a, b) => Number(a.active) - Number(b.active));
  const queryParams = new URLSearchParams(location.search);
  queryParams.set("path", window.path);
  history.replaceState(null, "", "?" + queryParams.toString());
}
