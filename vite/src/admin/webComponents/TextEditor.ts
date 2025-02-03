import { EditorView } from "prosemirror-view";
import { EditorState } from "prosemirror-state";
import {
  schema,
  defaultMarkdownParser,
  defaultMarkdownSerializer,
} from "prosemirror-markdown";
import { exampleSetup } from "prosemirror-example-setup";

export class TextEditor extends HTMLElement {
  #textarea: HTMLTextAreaElement;
  #view: EditorView;
  #editorWrapper: HTMLElement;
  #form: HTMLFormElement;

  constructor() {
    super();
    this.#textarea = this.querySelector("textarea")!;
    this.#form = this.parentElement as HTMLFormElement;
    this.#textarea.style.setProperty("display", "none");
    this.#editorWrapper = document.createElement("div");
    this.#editorWrapper.classList.add("wrapper");
    this.append(this.#editorWrapper);

    this.#view = new EditorView(this.#editorWrapper, {
      state: EditorState.create({
        doc: defaultMarkdownParser.parse(this.#textarea.value),
        plugins: exampleSetup({ schema }),
      }),
    });

    this.#form.addEventListener("formdata", this.#handleFormData.bind(this));
  }

  get #content() {
    return defaultMarkdownSerializer.serialize(this.#view.state.doc);
  }

  #handleFormData(e: FormDataEvent) {
    const formData = e.formData;
    formData.set(this.#textarea.name, this.#content);
  }
}
