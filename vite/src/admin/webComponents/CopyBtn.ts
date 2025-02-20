export class CopyBtn extends HTMLElement {
  #btn: HTMLButtonElement;
  #content: string | null = null;
  constructor() {
    super();
    this.#btn = this.querySelector("button")!;
  }

  connectedCallback() {
    this.#content = this.getAttribute("data-content");
    if (!this.#content) {
      this.style.setProperty("display", "none");
      return;
    }
    this.#btn.addEventListener("click", this.#handleClick);
  }

  disconnectedCallback() {
    this.#btn.removeEventListener("click", this.#handleClick);
  }

  #handleClick = () => {
    if (this.#content) navigator.clipboard.writeText(this.#content);
  };
}
