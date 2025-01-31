import "./styles/index.css";
import "@picocss/pico/css/pico.css";
import { TextEditor } from "./webComponents/TextEditor";
import "@grafikart/drop-files-element";

customElements.define("text-editor", TextEditor);
