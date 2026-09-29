import {fileURLToPath} from 'node:url';
import {build} from 'esbuild';
import {readFile,mkdir} from 'node:fs/promises';
const root=new URL('../wordpress/sphere/',import.meta.url);
const out=new URL('generated/',root);await mkdir(out,{recursive:true});
const css=await Promise.all(['design','motion','wordpress'].map(n=>readFile(new URL(n+'.css',root),'utf8')));
await build({stdin:{contents:css.join('\n'),loader:'css',resolveDir:fileURLToPath(root)},outfile:fileURLToPath(new URL('site.css',out)),minify:true,legalComments:'none'});
for(const name of ['app','motion','contact'])await build({entryPoints:[fileURLToPath(new URL(name+'.js',root))],outfile:fileURLToPath(new URL(name+'.js',out)),minify:true,legalComments:'none',target:'es2020'});
