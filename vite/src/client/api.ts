type BaseFile = {
  path: string;
  parent: string | null;
  name: string;
};

export type LinkFile = {
  type: "link";
  url: string;
} & BaseFile;

export type ImageFile = {
  type: "image";
  src: string;
} & BaseFile;

export type AudioFile = {
  type: "audio";
  src: string;
} & BaseFile;

export type MarkdownFile = {
  type: "markdown";
  content: string;
} & BaseFile;

export type UnknownFile = {
  type: "unknown";
  src: string;
} & BaseFile;

export type Directory = {
  type: "directory";
  childrens: (
    | LinkFile
    | ImageFile
    | AudioFile
    | UnknownFile
    | Omit<MarkdownFile, "content">
    | Omit<Directory, "childrens">
  )[];
} & BaseFile;

export type FileData =
  | LinkFile
  | ImageFile
  | AudioFile
  | UnknownFile
  | MarkdownFile
  | Directory;

export async function fetchApi(path: string): Promise<FileData> {
  const url = new URL("http://localhost:8000/api");
  url.searchParams.append("path", path);
  const response = await fetch(url);
  if (!response.ok) throw new Error(`${response.statusText}`);
  return (await response.json()) as FileData;
}
