import { mount } from "svelte";
import App from "./client/App.svelte";
import "./client/style.css";

const app = mount(App, {
  target: document.getElementById("app")!,
});

export default app;
