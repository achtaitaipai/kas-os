import "./admin/style.css";
import "@picocss/pico/css/pico.css";
import { TextEditor } from "./admin/webComponents/TextEditor";
import "@grafikart/drop-files-element";

customElements.define("text-editor", TextEditor);
