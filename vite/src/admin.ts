import "./admin/style.css";
import { CopyBtn } from "./admin/webComponents/CopyBtn";
import { TextEditor } from "./admin/webComponents/TextEditor";
import "@grafikart/drop-files-element";

customElements.define("text-editor", TextEditor);

customElements.define("copy-button", CopyBtn);
