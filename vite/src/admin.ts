import "./admin/style.css";
import { CopyBtn } from "./admin/webComponents/CopyBtn";
import { TextEditor } from "./admin/webComponents/TextEditor";

customElements.define("text-editor", TextEditor);

customElements.define("copy-button", CopyBtn);
