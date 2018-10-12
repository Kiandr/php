import path from 'path';
import fs from 'fs';

export default function getDirsSync (dir) {
    return fs.readdirSync(dir).filter(function(file) {
        return fs.statSync(path.join(dir, file)).isDirectory();
    });
}